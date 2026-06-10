
<div class="panel panel-default /*acco-despatchorders*/ acco-six tab">
    <script src="https://cdn.ckeditor.com/4.6.0-441b33b/full-all/ckeditor/ckeditor.js"></script>
    <script src="<?php echo Yii::$app->request->baseUrl; ?>/jsnew/procurement/despatchorder.js" type="text/javascript"></script>
    <!--<input type="radio" id="rd1" name="rd">-->
    <div class="panel-heading">
      <h4 class="panel-title " id="despatch-Order">
        <a data-toggle="collapse" data-parent="#accordionindex" href="#collapsedespatch">
        <span class="icon-envelope"></span>Despatch Order</a>
      </h4>
    </div>
    <div id="collapsedespatch" class="tab-content panel-collapse panel-collapse collapse">
        <div class="panel-body"  id="despatchorderlistsection">
            <div class="row" id="deshistory">
                                <div class="col-md-10 despshwnav">
                                <ul class="nav nav-tabs text-center">
			
                                    <li class="depurrr"><a data-toggle="pill" href="#depoopord" id="desppurchor"><span class="icon-shopping_cart"></span> Purchase Orders</a></li>
                                    <li><a data-toggle="pill" href="#dewrord" id="despworko"><span class="icon-shopping_cart"></span> Work Orders</a></li>
                                    <li><a data-toggle="pill" href="#dedirrord" id="despdirec"><span class="icon-shopping_cart"></span> Direct Work Orders</a></li>
                                    <li><a data-toggle="pill" href="#delesord" id="despleaso"><span class="icon-shopping_cart"></span> Lease Orders</a></li>
                                    <li><a data-toggle="pill" href="#dedesord" id="despdespto"><span class="icon-shopping_cart"></span> P&M Movement</a></li>
                                </ul>
                                </div>
                                <div class="col-md-2 text-right" id="deshistorys">
                                    <button type="button" class="btn btn-primary historydes" id="historydes" title="History"><span></span> History</button>
                                </div>
                            </div>
            <input type="hidden" id="despatchordersearch">
            <input type="hidden" id="indetifydesp">
          
           <div class="despatchlist">
          
            <table class="table table-bordered despatch-table" id="despatchordertable">
                <thead>
                        <!--<tr>
                            <th colspan="9"><span style="float: left;font-weight: bold;padding: 10px;width: 100%;text-align: center">Cart</span></th>
                        </tr>-->
                        <tr>
                            <th style="width: 87px;"></th>
                            <th style="width: 313px;">Date</th>
                            <th>Order Type</th>
                            
                            <th>Vendor Name</th>
                            <th>Amount</th>
                            <th colspan="4" style="width:14%;"></th>
                        </tr>
                </thead>
                        <tbody  id="despatchorderitems">

                        </tbody>        
            </table>
        </div>

        <div id="amenddata" style="display: none;">
            
        </div>

        <div id="historiesdsptch" style="display: none;">

            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" id="dsptchback" style="padding-bottom: 10px;">
                    <button type="button" class="btn btn-primary despatchback" id="despatchback">Back</button>
                </div></div>

             <table class="table table-bordered despatchhstry-table" id="despatchhistrytable" style="display: none;">
                <thead>
                    <tr>
                            <th style="width: 87px;"></th>
                            <th style="width: 313px;">Date</th>
                            <th>Activity Name</th>
                            
                            <th>Vendor Name</th>
                            <th>Amount</th>
                            
                        </tr>
                </thead>
                        <tbody  id="historydespatchitems">

                        </tbody>        
            </table>
        </div>

            <div class="preloader" style="display: none;">
                <div colspan="9" align="center">
                    <img src="<?php echo Yii::$app->request->baseUrl; ?>/images/loader.gif" align="middle"> 
                </div>
            </div>
            <div class="" ></div>
          
        </div>
    </div>

    <div class="acc_container" style="display: none;">
        <div class="block">
            <div class="jumbotron">
                <div>
                    
                    <div class="row show-grid">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <script>
        CKEDITOR.replace( 'orderbody', {
            // Define the toolbar: http://docs.ckeditor.com/#!/guide/dev_toolbar
            // The full preset from CDN which we used as a base provides more features than we need.
            // Also by default it comes with a 3-line toolbar. Here we put all buttons in a single row.
            toolbar: [
                { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
                { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
                { name: 'insert', items: [ 'Image', 'Table' ] },
                { name: 'tools', items: [ 'Maximize' ] },
                { name: 'editing', items: [ 'Scayt' ] }
            ],

            // Since we define all configuration options here, let's instruct CKEditor to not load config.js which it does by default.
            // One HTTP request less will result in a faster startup time.
            // For more information check http://docs.ckeditor.com/#!/api/CKEDITOR.config-cfg-customConfig
            customConfig: '',

            // Sometimes applications that convert HTML to PDF prefer setting image width through attributes instead of CSS styles.
            // For more information check:
            //  - About Advanced Content Filter: http://docs.ckeditor.com/#!/guide/dev_advanced_content_filter
            //  - About Disallowed Content: http://docs.ckeditor.com/#!/guide/dev_disallowed_content
            //  - About Allowed Content: http://docs.ckeditor.com/#!/guide/dev_allowed_content_rules
            disallowedContent: 'img{width,height,float}',
            extraAllowedContent: 'img[width,height,align]',

            // Enabling extra plugins, available in the full-all preset: http://ckeditor.com/presets-all
            //extraPlugins: 'tableresize,uploadimage,uploadfile',

            /*********************** File management support ***********************/
            // In order to turn on support for file uploads, CKEditor has to be configured to use some server side
            // solution with file upload/management capabilities, like for example CKFinder.
            // For more information see http://docs.ckeditor.com/#!/guide/dev_ckfinder_integration

            // Uncomment and correct these lines after you setup your local CKFinder instance.
            // filebrowserBrowseUrl: 'http://example.com/ckfinder/ckfinder.html',
            // filebrowserUploadUrl: 'http://example.com/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
            /*********************** File management support ***********************/

            // Make the editing area bigger than default.
            width:555,height:542,

            // An array of stylesheets to style the WYSIWYG area.
            // Note: it is recommended to keep your own styles in a separate file in order to make future updates painless.
            contentsCss: [ 'https://cdn.ckeditor.com/4.6.0-441b33b/full-all/ckeditor/contents.css'],

            // This is optional, but will let us define multiple different styles for multiple editors using the same CSS file.
            bodyClass: 'document-editor',

            // Reduce the list of block elements listed in the Format dropdown to the most commonly used.
            format_tags: 'p;h1;h2;h3;pre',

            // Simplify the Image and Link dialog windows. The "Advanced" tab is not needed in most cases.
            removeDialogTabs: 'image:advanced;link:advanced',

            // Define the list of styles which should be available in the Styles dropdown list.
            // If the "class" attribute is used to style an element, make sure to define the style for the class in "mystyles.css"
            // (and on your website so that it rendered in the same way).
            // Note: by default CKEditor looks for styles.js file. Defining stylesSet inline (as below) stops CKEditor from loading
            // that file, which means one HTTP request less (and a faster startup).
            // For more information see http://docs.ckeditor.com/#!/guide/dev_styles
            stylesSet: [
                /* Inline Styles */
                { name: 'Marker', element: 'span', attributes: { 'class': 'marker' } },
                { name: 'Cited Work', element: 'cite' },
                { name: 'Inline Quotation', element: 'q' },

                /* Object Styles */
                {
                    name: 'Special Container',
                    element: 'div',
                    styles: {
                        padding: '5px 10px',
                        background: '#eee',
                        border: '1px solid #ccc'
                    }
                },
                {
                    name: 'Compact table',
                    element: 'table',
                    attributes: {
                        cellpadding: '5',
                        cellspacing: '0',
                        border: '1',
                        bordercolor: '#ccc'
                    },
                    styles: {
                        'border-collapse': 'collapse',
                        'width':'100%'
                    }
                },
                { name: 'Borderless Table', element: 'table', styles: { 'border-style': 'hidden', 'background-color': '#E6E6FA' } },
                { name: 'Square Bulleted List', element: 'ul', styles: { 'list-style-type': 'square' } }
            ]
        } );
    </script>

</div>