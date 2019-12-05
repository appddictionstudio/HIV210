
var site_link = "http://hiv210.org/";

function allowDrop(ev) {
    ev.preventDefault();
}
function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}
function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
}
jQuery('#review1').click(function(){     
    var t = 0;
    var c = 0;
    jQuery( ".drop-div" ).each(function( i ) {
        t++;
        if(jQuery(this).find('.ans').val() == jQuery(this).find('.dg').html()){
            c++;
        }
    });
    if(t==c) {
        jQuery.post( site_link+"submit-module.php", { 'case': 'module1', 'questions': c }, function( data ) {
            if(data == 'success'){
                alert('Sucessfully submitted your details');
                location.href='./module2/';
            }
        });
        
    }
    else alert('Correct answer '+c+' Out of '+t+' Questions'+'\n'+' For submit your details with us you need to collect 100% marks');

});

jQuery('#review2').click(function(){
    var t = 0;
    var c = 0;
    jQuery( ".drop-div" ).each(function( i ) {
        t++;
        if(jQuery(this).find('.ans').val() == jQuery(this).find('.dg').html()){
            c++;
        }
    });
    if(t==c) {
        jQuery.post( site_link+"submit-module.php", { 'case': 'module2', 'questions': c }, function( data ) {
            if(data == 'success'){
                alert('Sucessfully submitted your details');
                location.href='./module3/';
            }
        });
        
    }
    else alert('Correct answer '+c+' Out of '+t+' Questions'+'\n'+' For submit your details with us you need to collect 100% marks');
  
}); 
jQuery('#review3').click(function(){
    var t = 0;
    var c = 0;
    jQuery( ".drop-div" ).each(function( i ) {
        t++;
        if(jQuery(this).find('.ans').val() == jQuery(this).find('.dg').html()){
            c++;
        }
    });
    if(t==c){
        jQuery.post( site_link+"submit-module.php", { 'case': 'module3', 'questions': c }, function( data ) {
            if(data == 'success'){
                alert('Sucessfully submitted your details');
                location.href='./module4/';
            }
        });        
    }
    else alert('Correct answer '+c+' Out of '+t+' Questions'+'\n'+' For submit your details with us you need to collect 100% marks');
});
jQuery('#review4').click(function(){
    var t = 0;
    var c = 0;
    jQuery( ".drop-div" ).each(function( i ) {
        t++;
        var answer = jQuery(this).find('.dg').html();
        var answer = answer.replace("&amp;", "&");
        if(jQuery(this).find('.ans').val() == answer){
            c++;
        }
    });
    if(t==c){
        jQuery.post( site_link+"submit-module.php", { 'case': 'module4', 'questions': c }, function( data ) {
            if(data == 'success'){
                alert('Sucessfully submitted your details');
                location.href='./module5/';
            }
        });        
    }
    else alert('Correct answer '+c+' Out of '+t+' Questions'+'\n'+' For submit your details with us you need to collect 100% marks');
});
jQuery('#review5').click(function(){  
    var t = 0;
    var c = 0;
    jQuery( ".drop-div" ).each(function( i ) {
        t++;
        var answer = jQuery(this).find('.dg').html();
        if(jQuery(this).find('.ans').val() == answer){
            c++;
        }
    });
    if(t==c){
        jQuery.post( site_link+"submit-module.php", { 'case': 'module5', 'questions': c }, function( data ) {
            if(data == 'success'){
                alert('Sucessfully submitted your details');
                location.href='./module6/';
            }
        });        
    }
    else alert('Correct answer '+c+' Out of '+t+' Questions'+'\n'+' For submit your details with us you need to collect 100% marks');
});
jQuery('#review6').click(function(){
    var t = 0;
    var c = 0;
    jQuery( ".drop-div" ).each(function( i ) {
        t++;
        if(jQuery(this).find('.ans').val() == jQuery(this).find('.dg').html()){
            c++;
        }
    });
    if(t==c){
        jQuery.post( site_link+"submit-module.php", { 'case': 'module6', 'questions': c }, function( data ) {
            if(data == 'success'){
                alert('Sucessfully submitted your details');
                location.href='./module7/';
            }
        });        
    }
    else alert('Correct answer '+c+' Out of '+t+' Questions'+'\n'+' For submit your details with us you need to collect 100% marks');

});