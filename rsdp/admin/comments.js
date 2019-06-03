
function delete_comment( p_script_url ) {
  var dc_form = document.comments;
  var dc_url;

  dc_url  = '?update=' + '-1';
  dc_url += '&com_rsdp='   + dc_form.com_rsdp.value;

  if (confirm('Delete Comment?')) {
    script = document.createElement('script');
    script.src = p_script_url;
    document.getElementsByTagName('head')[0].appendChild(script);
    show_file('../admin/comments.mysql.php' + dc_url);
  }
}

function attach_comment( p_script_url, update ) {
  var ac_form = document.comments;
  var ac_url;

  ac_url  = '?update='   + update;
  ac_url += '&id='       + ac_form.com_id.value;
  ac_url += "&com_rsdp=" + ac_form.com_rsdp.value;

  ac_url += '&com_task='      + '1';
  ac_url += "&com_text="      + encode_URI(ac_form.com_text.value);

  script = document.createElement('script');
  script.src = p_script_url + ac_url;
  document.getElementsByTagName('head')[0].appendChild(script);
}

$(function() {
  $( '#clickAddComment' ).click(function() {
    $( "#dialogComment" ).dialog('open');
  });

  $( "#dialogComment" ).dialog({
    autoOpen: false,
    modal: true,
    height: 400,
    width: 1000,
    show: 'slide',
    hide: 'slide',
    closeOnEscape: true,
    dialogClass: 'dialogWithDropShadow',
    close: function(event, ui) {
      $( "#dialogComment" ).hide();
    },
    buttons: [
      {
        text: "Cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Update Comment",
        click: function() {
          attach_comment('../admin/comments.mysql.php', 1);
          $( this ).dialog( "close" );
        }
      },
      {
        text: "Add Comment",
        click: function() {
          attach_comment('../admin/comments.mysql.php', 0);
          $( this ).dialog( "close" );
        }
      }
    ]
  });
});

