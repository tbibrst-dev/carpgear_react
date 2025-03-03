var fp_current_id = '';
jQuery(document).on('click','.flexible-printing-button-print', function( event ) {
    event.preventDefault();
    if ( jQuery(this).attr('disabled') == 'disabled' ) {
        return;
    }
    fp_current_id = jQuery(this).attr('id');
    jQuery(this).find('i').removeClass('fa-print');
    jQuery(this).find('i').addClass('fa-spinner');
    jQuery(this).find('i').addClass('fa-pulse');
    jQuery('.flexible-printing-button-print').attr( 'disabled','disabled' );
    jQuery('#spinner_' + jQuery(this).attr('id')).show();
    jQuery('#spinner_' + jQuery(this).attr('id')).css({visibility: 'visible'});

    if (jQuery().tipTip) {
        jQuery(this).attr('data-tip-org',jQuery(this).attr('data-tip'));
        jQuery(this).attr('data-tip',flexible_printing.printing_message);
        jQuery(this).tipTip({'attribute': 'data-tip'});
    }
    jQuery(this).trigger('mouseover');


    var ajax_data = {
        'action'		: 'flexible_printing',
        'fp_action'	    : 'print',
        'id'            : jQuery(this).attr('id'),
    };

    jQuery.each( this.attributes, function( i, attrib ){
        var name = attrib.name;
        var value = attrib.value;
        if ( name.startsWith('data') ) {
            ajax_data[name] = value;
        }
    });

    jQuery.ajax({
        url		: flexible_printing.ajax_url,
        data	: ajax_data,
        method	: 'POST',
        dataType: 'JSON',
        success: function( data ) {
            console.log(data);
            if ( data.status == 'ok' ) {
                jQuery('#flexible_printing_message_' + data.id).html(data.message);
                jQuery('#flexible_printing_message_' + data.id).show();
                if (jQuery().tipTip) {
                    jQuery('#' + data.id).attr('data-tip-org', jQuery('#' + data.id).attr('data-tip'));
                    jQuery('#' + data.id).attr('data-tip', data.message);
                    jQuery('#' + data.id).tipTip({'attribute': 'data-tip'});
                }
                jQuery('#' + data.id).find('i').addClass('fa-check');
                jQuery('#' + data.id).find('i').removeClass('fa-spinner');
                jQuery('#' + data.id).find('i').removeClass('fa-pulse');
                jQuery('#' + data.id).trigger('mouseover');
                setTimeout(function () {
                    jQuery('#flexible_printing_message_' + data.id).hide();
                }, 5000);
            }
            else {
                var message = data.message;
                jQuery( '#flexible_printing_message_' + fp_current_id ).html(message);
                jQuery( '#flexible_printing_message_' + fp_current_id ).show();
                if (jQuery().tipTip) {
                    jQuery('#' + fp_current_id).attr('data-tip-org',jQuery('#' + fp_current_id).attr('data-tip'));
                    jQuery('#' + fp_current_id).attr('data-tip',message);
                    jQuery('#' + fp_current_id).tipTip({'attribute': 'data-tip'});
                }
                jQuery('#' + fp_current_id).find('i').addClass('fa-exclamation');
                jQuery('#' + fp_current_id).find('i').removeClass('fa-spinner');
                jQuery('#' + fp_current_id).find('i').removeClass('fa-pulse');
                jQuery('#' + fp_current_id).trigger('mouseover');
            }
        },
        error: function ( xhr, ajaxOptions, thrownError ) {
            var message = xhr.status + ': ' + thrownError;
            //alert( message );
            jQuery( '#flexible_printing_message_' + fp_current_id ).html(message);
            jQuery( '#flexible_printing_message_' + fp_current_id ).show();
            if (jQuery().tipTip) {
                jQuery('#' + fp_current_id).attr('data-tip-org',jQuery('#' + fp_current_id).attr('data-tip'));
                jQuery('#' + fp_current_id).attr('data-tip',message);
                jQuery('#' + fp_current_id).tipTip({'attribute': 'data-tip'});
            }
            jQuery('#' + fp_current_id).find('i').addClass('fa-exclamation');
            jQuery('#' + fp_current_id).find('i').removeClass('fa-spinner');
            jQuery('#' + fp_current_id).find('i').removeClass('fa-pulse');
            jQuery('#' + fp_current_id).trigger('mouseover');
        },
        complete: function() {
            jQuery('.flexible-printing-button-print').removeAttr('disabled','disabled');
            jQuery('.flexible-printing-spinner').css({visibility: 'hidden'});
            var local_current_id = fp_current_id;
            setTimeout( function() { flexible_printing_timeout(local_current_id) }, 5000 );
        }
    });
})


function flexible_printing_timeout( id ) {
    if (jQuery().tipTip) {
        jQuery('#' + id).attr('data-tip',jQuery('#' + id).attr('data-tip-org'));
        jQuery('#' + id).tipTip({'attribute': 'data-tip'});
    }
    jQuery('#' + id).find('i').addClass('fa-print');
    jQuery('#' + id).find('i').removeClass('fa-exclamation');
    jQuery('#' + id).find('i').removeClass('fa-check');
    jQuery('#' + id).find('i').removeClass('fa-spinner');
    jQuery('#' + id).find('i').removeClass('fa-pulse');
}