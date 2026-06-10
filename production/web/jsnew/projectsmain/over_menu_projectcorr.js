$(function(){

    $(document).on('click', '.navbar-nav .overNow9', function(e){
        e.preventDefault();
        // Close all other panels first
        var others = {
            '.overNow':  '.menu-popup-cntnr',
            '.overNow1': '.menu1-popup-cntnr',
            '.overNow2': '.finmenu-popup-cntnr',
            '.overNow4': '.menu4-popup-cntnr',
            '.overNow5': '.menu5-popup-cntnr',
            '.overNow8': '.menu8-popup-cntnr'
        };
        $.each(others, function(cls, cntnr){
            if($(cls).hasClass('active')){
                $(cls).removeClass('active');
                $(cntnr).removeClass('active');
                $('body').css('overflow-y', 'auto');
            }
        });

        $('.overNow9').toggleClass('active');
        if($('.overNow9').hasClass('active')){
            $('.menu9-popup-cntnr').addClass('active');
            $('body').css('overflow-y', 'hidden');
        } else {
            $('.menu9-popup-cntnr').removeClass('active');
            $('body').css('overflow-y', 'auto');
        }
    });

    // Search
    $(document).on('click', '#corr_search_btn', function(){
        var subject  = $('#corr_search_subject').val();
        var ref      = $('#corr_search_ref').val();
        var date     = $('#corr_search_date').val();
        var keywords = $('#corr_search_keywords').val();

        if(subject === '' && ref === '' && date === '' && keywords === ''){
            $('#corr_results').html('<p class="text-muted">Enter a search term and click Search.</p>');
            return;
        }

        $('#corr_search_btn').attr('disabled', true).text('Searching...');
        $.ajax({
            type: 'POST',
            url: '../projects/searchprojectfiles',
            dataType: 'json',
            data: { file_type:'correspondence', subject:subject, ref:ref, date:date, keywords:keywords },
            success: function(data){
                $('#corr_search_btn').attr('disabled', false).html('<span class="icon-search5"></span> Search');
                if(data.files && data.files.length > 0){
                    var html = '<table class="table table-hover table-bordered" style="font-size:13px;">' +
                        '<thead><tr><th>Filename</th><th>Project</th><th>Uploaded</th><th style="width:80px;"></th></tr></thead><tbody>';
                    $.each(data.files, function(i, f){
                        html += '<tr>' +
                            '<td>' + $('<span>').text(f.original_name).html() + '</td>' +
                            '<td>' + $('<span>').text(f.project_name).html() + '</td>' +
                            '<td>' + f.uploaded_at + '</td>' +
                            '<td><button class="btn btn-sm btn-primary open-doc-viewer-corr" ' +
                                'data-filename="' + f.filename + '" ' +
                                'data-title="' + $('<span>').text(f.original_name).html() + '">' +
                                '<span class="icon-eye"></span> Open</button></td>' +
                            '</tr>';
                    });
                    html += '</tbody></table>';
                    $('#corr_results').html(html);
                } else {
                    $('#corr_results').html('<p class="text-muted">No correspondence found.</p>');
                }
            },
            error: function(){
                $('#corr_search_btn').attr('disabled', false).html('<span class="icon-search5"></span> Search');
                $('#corr_results').html('<p class="text-danger">Search failed. Please try again.</p>');
            }
        });
    });

    // Open viewer
    $(document).on('click', '.open-doc-viewer-corr', function(){
        var filename = $(this).data('filename');
        var title    = $(this).data('title');
        var url = '../web/uploads/projects/' + filename;
        var fullUrl  = window.location.origin + '/production/web/uploads/projects/' + filename;

        $('#corrViewerTitle').text(title);
        $('#corrViewerFrame').attr('src', url);
        $('#corrDownloadBtn').attr('href', url);
        $('#corrShareWhatsapp').attr('href',
            'https://api.whatsapp.com/send?text=' + encodeURIComponent(title + ' ' + fullUrl));
        $('#corrShareEmail').attr('href',
            'mailto:?subject=' + encodeURIComponent(title) +
            '&body=' + encodeURIComponent('Please find the document here: ' + fullUrl));
        $('#corrPrintBtn').off('click').on('click', function(){
            var w = window.open(url);
            if(w){ w.onload = function(){ w.print(); }; }
        });
        $('#corrViewerModal').modal('show');
    });

    // Also trigger search on Enter key
    $(document).on('keypress', '#corr_search_subject, #corr_search_ref, #corr_search_keywords', function(e){
        if(e.which === 13){ $('#corr_search_btn').trigger('click'); }
    });

});
