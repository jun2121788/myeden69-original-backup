<?php
$params = Params::getParamsAsArray();

if(isset($params['action'])) {
    $params['_action'] = isset($params['action']) ? $params['action'] : '';
}
$params['_page'] = isset($params['page']) ? $params['page'] : '';
unset($params['action']);
unset($params['page']);

foreach($params as $k => $v) {
    if(is_array($v)) {
        foreach($v as $_k => $_v) {
            $url_params[] = $k.sprintf('[%s]=%s', $_k, $_v);
        }
    } else {
        $url_params[] = sprintf('%s=%s', $k, $v);
    }
}
$sParams = '&'.implode('&', $url_params);
?>

<script>
    var total_pages = <?php echo (osc_search_total_pages()!='') ? osc_search_total_pages() : 0; ?>;
    var scroll_iPage = 2;
    $('#js-load-more-listings').click(function () {
        var url = '<?php echo osc_ajax_hook_url('load_more_listing' ). $sParams; ?>&iPage=' + scroll_iPage;
        var jqxhr = $.ajax({
            type: "POST",
            url: url,
            dataType: 'html',
            beforeSend: function( xhr ) {
                $('#js-load-more-listings').hide();
                $('#js-load-more-listings-loading').show();
            },
            statusCode: {
                404: function() {
                  $('#js-load-more-listings').hide();
                  $('#js-load-more-listings-loading').hide();

                }
              },
            success: function (data) {
                var html = $.parseHTML(data);
                container.append( html ).masonry( 'appended', html );
                if(scroll_iPage==total_pages) {
                    $('.wrapper-more-listings').hide();
                } else {
                    scroll_iPage++;
                }
            }
        });
        jqxhr.always(function () {
            $('#js-load-more-listings').show();
            $('#js-load-more-listings-loading').hide();
            setTimeout(function(){ container.masonry(); }, 500);
        });
        return false;
    });
</script>
