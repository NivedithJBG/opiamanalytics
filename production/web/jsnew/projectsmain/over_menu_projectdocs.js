$(function(){

    $(document).on('click', '.navbar-nav .overNow8', function(e){
        e.preventDefault();
        var others = {
            '.overNow':  '.menu-popup-cntnr',
            '.overNow1': '.menu1-popup-cntnr',
            '.overNow2': '.finmenu-popup-cntnr',
            '.overNow4': '.menu4-popup-cntnr',
            '.overNow5': '.menu5-popup-cntnr',
            '.overNow9': '.menu9-popup-cntnr'
        };
        $.each(others, function(cls, cntnr){
            if($(cls).hasClass('active')){
                $(cls).removeClass('active');
                $(cntnr).removeClass('active');
                $('body').css('overflow-y', 'auto');
            }
        });
        $('.overNow8').toggleClass('active');
        if($('.overNow8').hasClass('active')){
            $('.menu8-popup-cntnr').addClass('active');
            $('body').css('overflow-y', 'hidden');
        } else {
            $('.menu8-popup-cntnr').removeClass('active');
            $('body').css('overflow-y', 'auto');
        }
    });

    // Core search function
    var docsSearchTimer = null;
    function runDocsSearch(){
        var subject  = $('#docs_search_subject').val();
        var ref      = $('#docs_search_ref').val();
        var date     = $('#docs_search_date').val();
        var keywords = $('#docs_search_keywords').val();

        if(subject === '' && ref === '' && date === '' && keywords === ''){
            $('#docs_results').html('<p class="text-muted">Select a project or start typing to search.</p>');
            return;
        }

        $('#docs_search_btn').attr('disabled', true).html('<span class="icon-spinner3"></span>');
        $.ajax({
            type: 'POST',
            url: '../projects/searchprojectfiles',
            dataType: 'json',
            data: { file_type:'documents', subject:subject, ref:ref, date:date, keywords:keywords },
            success: function(data){
                $('#docs_search_btn').attr('disabled', false).html('<span class="icon-search5"></span> Search');
                if(data.files && data.files.length > 0){
                    var thStyle = 'background:#b0b4c0;color:#111;font-weight:700;border:1px solid #6e7280;padding:8px 10px;';
                    var html = '<table style="font-size:13px;color:#000;table-layout:fixed;width:100%;border-collapse:collapse;">' +
                        '<colgroup>' +
                            '<col style="width:5%;">' +
                            '<col style="width:36%;">' +
                            '<col style="width:26%;">' +
                            '<col style="width:20%;">' +
                            '<col style="width:13%;">' +
                        '</colgroup>' +
                        '<thead><tr>' +
                            '<th style="' + thStyle + '">Sl No</th>' +
                            '<th style="' + thStyle + '">Name of the Document</th>' +
                            '<th style="' + thStyle + '">Project</th>' +
                            '<th style="' + thStyle + '">Date</th>' +
                            '<th style="' + thStyle + '"></th>' +
                        '</tr>' +
                        '<tr><td colspan="5" style="padding:0;height:3px;background:#3a3d4a;border:none;"></td></tr>' +
                        '</thead><tbody>';
                    var tdStyle = 'border:1px solid #3a3d4a;border-bottom:2px solid #3a3d4a;padding:7px 10px;vertical-align:middle;';
                    var tdFirstRowStyle = tdStyle;
                    $.each(data.files, function(i, f){
                        var rowBg = i % 2 === 0 ? '#ffffff' : '#f5f6f8';
                        var td = i === 0 ? tdFirstRowStyle : tdStyle;
                        html += '<tr id="doc-row-' + f.id + '" style="background:' + rowBg + ';">' +
                            '<td style="' + td + 'color:#000;">' + (i + 1) + '</td>' +
                            '<td style="' + td + 'color:#000;word-wrap:break-word;">' + $('<span>').text(f.original_name).html() + '</td>' +
                            '<td style="' + td + 'color:#000;word-wrap:break-word;">' + $('<span>').text(f.project_name).html() + '</td>' +
                            '<td style="' + td + 'color:#000;">' + f.uploaded_at + '</td>' +
                            '<td style="' + td + '">' +
                                '<div style="display:flex; justify-content:flex-end; gap:8px; align-items:center;">' +
                                    '<button class="btn btn-sm open-doc-viewer-docs" ' +
                                        'data-filename="' + f.filename + '" ' +
                                        'data-title="' + $('<span>').text(f.original_name).html() + '" ' +
                                        'style="background:#337ab7;color:#fff;border:1px solid #2e6da4;border-radius:50px;padding:5px 14px;">' +
                                        '<span class="icon-eye"></span> Open</button>' +
                                    '<button class="btn btn-sm remove-doc-file" ' +
                                        'data-id="' + f.id + '" ' +
                                        'data-filename="' + $('<span>').text(f.original_name).html() + '" ' +
                                        'style="background:#d9534f;color:#fff;border:1px solid #c9302c;border-radius:50px;padding:5px 14px;">' +
                                        '<span class="icon-trash"></span> Remove</button>' +
                                '</div>' +
                            '</td>' +
                            '</tr>';
                    });
                    html += '</tbody></table>';
                    $('#docs_results').html(html);
                } else {
                    $('#docs_results').html('<p class="text-muted">No documents found.</p>');
                }
            },
            error: function(){
                $('#docs_search_btn').attr('disabled', false).html('<span class="icon-search5"></span> Search');
                $('#docs_results').html('<p class="text-danger">Search failed. Please try again.</p>');
            }
        });
    }

    // Live search — debounced 400ms for text inputs
    $(document).on('input', '#docs_search_subject, #docs_search_keywords', function(){
        clearTimeout(docsSearchTimer);
        docsSearchTimer = setTimeout(runDocsSearch, 400);
    });

    // Instant search on dropdown and date change
    $(document).on('change', '#docs_search_ref, #docs_search_date', function(){
        clearTimeout(docsSearchTimer);
        runDocsSearch();
    });

    // Search button click still works
    $(document).on('click', '#docs_search_btn', function(){
        clearTimeout(docsSearchTimer);
        runDocsSearch();
    });

    // Open viewer
    $(document).on('click', '.open-doc-viewer-docs', function(){
        var filename = $(this).data('filename');
        var title    = $(this).data('title');
        var url      = '../uploads/projects/' + filename;
        var fullUrl  = window.location.origin + '/production/web/uploads/projects/' + filename;

        $('#docViewerTitle').text(title);
        $('#docViewerFrame').attr('src', url);
        $('#docDownloadBtn').attr('href', url);
        $('#docShareWhatsapp').attr('href',
            'https://api.whatsapp.com/send?text=' + encodeURIComponent(title + ' ' + fullUrl));
        $('#docShareEmail').attr('href',
            'mailto:?subject=' + encodeURIComponent(title) +
            '&body=' + encodeURIComponent('Please find the document here: ' + fullUrl));
        $('#docPrintBtn').off('click').on('click', function(){
            var w = window.open(url);
            if(w){ w.onload = function(){ w.print(); }; }
        });
        $('#docViewerModal').modal('show');
    });

    // Remove document
    $(document).on('click', '.remove-doc-file', function(){
        var $btn     = $(this);
        var fileId   = $btn.data('id');
        var filename = $btn.data('filename');
        if(!confirm('Remove "' + filename + '"? This cannot be undone.')) return;
        $btn.attr('disabled', true);
        $.ajax({
            type: 'POST',
            url: '../projects/deleteprojectfile',
            dataType: 'json',
            data: { fileid: fileId },
            success: function(data){
                if(data.error === 'No'){
                    $('#doc-row-' + fileId).remove();
                } else {
                    $btn.attr('disabled', false);
                    alert('Could not remove the file. Please try again.');
                }
            },
            error: function(){
                $btn.attr('disabled', false);
                alert('Remove failed. Please try again.');
            }
        });
    });

});
