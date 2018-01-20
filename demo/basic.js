$(document).ready(function () {

	$('.star').on('click', function () {
      $(this).toggleClass('star-checked');
    });

    $('.ckbox label').on('click', function () {
      $(this).parents('tr').toggleClass('selected');
    });

    $('.btn-filter').on('click', function () {
      var $target = $(this).data('target');
      if ($target != 'all') {
        $('.table tr').css('display', 'none');
        $('.table tr[data-status="' + $target + '"]').fadeIn('slow');
      } else {
        $('.table tr').css('display', 'none').fadeIn('slow');
      }
    });

 });
jQuery(function(){
         /*jQuery('#showall').click(function(){
               jQuery('.targetDiv').show();
        });*/
        jQuery('.showSingle').click(function(){
				if ($(this).is(":checked")) {
					//jQuery('.targetDiv').hide();
					jQuery('#div'+$(this).attr('target')).show();
				}            
				else
				{
					//jQuery('.targetDiv').hide();
					jQuery('#div'+$(this).attr('target')).hide();
					//jQuery('#div'+$(this).attr('target')).show();
				}
        });
});

$(function () {
    $( '#table' ).searchable({
        striped: true,
        oddRow: { 'background-color': '#f5f5f5' },
        evenRow: { 'background-color': '#fff' },
        searchType: 'fuzzy'
    });
    
    $( '#searchable-container' ).searchable({
        searchField: '#container-search',
        selector: '.row',
        childSelector: '.col-xs-4',
        show: function( elem ) {
            elem.slideDown(100);
        },
        hide: function( elem ) {
            elem.slideUp( 100 );
        }
    })
});
/*$(function () {
			/*$("#checkbox1").click(function () {
				if ($(this).is(":checked")) {
					$("#txtHint").show();
				} else {
					$("#txtHint").hide();
				}
			});
			$(".check").click(function () {
				if ($(this).is(":checked")) {
					$("#txtHint").show();
				} else {
					$("#txtHint").hide();
				}
			});
});*/
/*
$(document).ready(function () {

	$('.star').on('click', function () {
      $(this).toggleClass('star-checked');
    });

    $('.ckbox label').on('click', function () {
      $(this).parents('tr').toggleClass('selected');
    });

    $('.btn-filter').on('click', function () {
      var $target = $(this).data('target');
      if ($target != 'all') {
        $('.table tr').css('display', 'none');
        $('.table tr[data-status="' + $target + '"]').fadeIn('slow');
      } else {
        $('.table tr').css('display', 'none').fadeIn('slow');
      }
    });

 });*/
 function showUser(str) {
    if (str == "") {
        document.getElementById("div1").innerHTML = "";
        return;
    } else {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) 
			{
                document.getElementById("div1").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","getuser.php?q="+str,true);
        xmlhttp.send();
    }
}


