function abonnement() {

    $("#sortie_lieu").change(function(event){
        $.ajax({
            url:        '/lieu/ajax/adresse',
            type:       'POST',
            dataType:   'json',

            success: function(data, status) {
                alert(" l'adresse est trouvée");
                /*
                for(i = 0; i < data.length; i++) {
                    lieu = data[i];

                    var adresse = $("<div></div>");
                    adresse.text(lieu['nomLieu']);
                    $('#test').append(adresse);
                }
                */
            },
            error : function(xhr, textStatus, errorThrown) {
                alert("Impossible de trouver l'adresse.");
            }
        });
    });


}
