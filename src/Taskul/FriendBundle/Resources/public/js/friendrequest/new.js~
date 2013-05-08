$(document).ready(function() {
$('#form_request').submit(function(event){
        $(this).ajaxSubmit({
            success: function (data){
                if(data.success === true){
                    route = Routing.generate('frequest_sended');
                    title = 'Listado de solicitudes';
                    loadPage(route);
                    History.pushState(null,title,route);
                }else{
                    console.log(data);
                }
            },
            error: function(jqXHR,textStatus,errorThrown){
                alert(jqXHR.responseText.message);
                console.log("Error of data:", jqXHR);
            }
        });
        // return false to prevent normal browser submit and page navigation
        return false;
    });
	menuColor('li#freq_ops_new');
});