<?php
namespace app\components;

use Yii;

/**
 * Web Push sender (RFC 8030 / 8291 / 8292) without external libraries:
 * VAPID (ES256 JWT) authorisation and aes128gcm payload encryption with
 * PHP's openssl. Devices subscribe from the Notifications window; their
 * subscriptions are kept in push_subscriptions. VAPID keys are generated once
 * and kept in runtime/vapid.json (never committed).
 */
class WebPush
{
    const SUBJECT = 'https://gospl.opiamanalytics.com';

    public static function keys()
    {
        $file = Yii::getAlias('@runtime') . '/vapid.json';
        if (is_file($file)) {
            $k = json_decode(file_get_contents($file), true);
            if (!empty($k['private']) && !empty($k['public'])) return $k;
        }
        $key = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        openssl_pkey_export($key, $pem);
        $k = ['private' => $pem, 'public' => self::b64u(self::rawPublic($key))];
        file_put_contents($file, json_encode($k));
        @chmod($file, 0600);
        return $k;
    }

    public static function ensureTables()
    {
        Yii::$app->db->createCommand("CREATE TABLE IF NOT EXISTS push_subscriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            endpoint TEXT NOT NULL,
            endpoint_hash CHAR(64) NOT NULL,
            p256dh VARCHAR(255) NOT NULL,
            auth VARCHAR(64) NOT NULL,
            user_agent VARCHAR(255) NULL,
            created_at DATETIME NOT NULL,
            last_ok DATETIME NULL,
            UNIQUE KEY uq_push_endpoint (endpoint_hash),
            KEY idx_push_user (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4")->execute();
    }

    /** Sends $msg (title, body, url, tag, badge) to every device of the user. Returns devices reached. */
    public static function sendToUser($userId, array $msg)
    {
        self::ensureTables();
        $db = Yii::$app->db;
        $subs = $db->createCommand("SELECT * FROM push_subscriptions WHERE user_id = :u", [':u' => (int)$userId])->queryAll();
        $ok = 0;
        foreach ($subs as $s) {
            $code = self::send($s, json_encode($msg));
            if ($code >= 200 && $code < 300) {
                $ok++;
                $db->createCommand()->update('push_subscriptions', ['last_ok' => date('Y-m-d H:i:s')], ['id' => $s['id']])->execute();
            } elseif ($code === 404 || $code === 410) {
                // The device unsubscribed or the subscription expired.
                $db->createCommand()->delete('push_subscriptions', ['id' => $s['id']])->execute();
            }
        }
        return $ok;
    }

    /** One push; returns the HTTP status from the push service (0 on failure). */
    public static function send(array $sub, $payload)
    {
        $keys = self::keys();
        $endpoint = $sub['endpoint'];
        $parts = parse_url($endpoint);
        $aud = $parts['scheme'] . '://' . $parts['host'];
        $jwt = self::jwt($aud, $keys['private']);
        $body = self::encrypt($payload, self::b64ud($sub['p256dh']), self::b64ud($sub['auth']));
        if ($body === null) return 0;
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                'Authorization: vapid t=' . $jwt . ', k=' . $keys['public'],
                'Content-Encoding: aes128gcm',
                'Content-Type: application/octet-stream',
                'TTL: 86400',
                'Urgency: high',
            ],
        ]);
        curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code;
    }

    private static function jwt($aud, $privatePem)
    {
        $h = self::b64u(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $p = self::b64u(json_encode(['aud' => $aud, 'exp' => time() + 12 * 3600, 'sub' => self::SUBJECT]));
        openssl_sign($h . '.' . $p, $der, $privatePem, OPENSSL_ALGO_SHA256);
        return $h . '.' . $p . '.' . self::b64u(self::derToRaw($der));
    }

    // RFC 8291 aes128gcm content encoding, single record.
    private static function encrypt($payload, $uaPublic, $authSecret)
    {
        if (strlen($uaPublic) !== 65 || strlen($authSecret) < 16) return null;
        $local = openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]);
        $asPublic = self::rawPublic($local);
        $peer = openssl_pkey_get_public(self::publicPem($uaPublic));
        if (!$peer) return null;
        $shared = openssl_pkey_derive($peer, $local, 32);
        if ($shared === false) return null;
        $ikm = hash_hkdf('sha256', $shared, 32, "WebPush: info\x00" . $uaPublic . $asPublic, $authSecret);
        $salt = random_bytes(16);
        $cek = hash_hkdf('sha256', $ikm, 16, "Content-Encoding: aes128gcm\x00", $salt);
        $nonce = hash_hkdf('sha256', $ikm, 12, "Content-Encoding: nonce\x00", $salt);
        $tag = '';
        $cipher = openssl_encrypt($payload . "\x02", 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, '', 16);
        if ($cipher === false) return null;
        return $salt . pack('N', 4096) . chr(65) . $asPublic . $cipher . $tag;
    }

    private static function rawPublic($key)
    {
        $d = openssl_pkey_get_details($key);
        return "\x04" . str_pad($d['ec']['x'], 32, "\x00", STR_PAD_LEFT) . str_pad($d['ec']['y'], 32, "\x00", STR_PAD_LEFT);
    }

    private static function publicPem($raw)
    {
        $der = hex2bin('3059301306072a8648ce3d020106082a8648ce3d030107034200') . $raw;
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    private static function derToRaw($der)
    {
        $o = 2;
        if (ord($der[1]) & 0x80) $o += ord($der[1]) & 0x7f;
        $rl = ord($der[$o + 1]); $r = substr($der, $o + 2, $rl); $o += 2 + $rl;
        $sl = ord($der[$o + 1]); $s = substr($der, $o + 2, $sl);
        $fix = function($x){ $x = ltrim($x, "\x00"); return str_pad($x, 32, "\x00", STR_PAD_LEFT); };
        return $fix($r) . $fix($s);
    }

    public static function b64u($s) { return rtrim(strtr(base64_encode($s), '+/', '-_'), '='); }
    public static function b64ud($s) { return base64_decode(strtr($s, '-_', '+/') . str_repeat('=', (4 - strlen($s) % 4) % 4)); }
}
