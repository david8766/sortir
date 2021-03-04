function abonnement() {

    $("#sortie_lieu").change(function(event){

        var bidon ="123"
        var json_data = JSON.stringify(bidon);

        $.ajax({
            url:        '/lieu/ajax/adresse',
            type:       'POST',
            dataType:   'json',
            data: { json_data },

            success: function(data, status) {
                for(i = 0; i < data.length; i++) {
                    /*
                    lieu = data[i];

                    var adresse = $("<div></div>");
                    adresse.text(lieu['nomLieu']);
                    */
                    var adresse = "hhhhhhhhhhhhhhhhhhh";
                    $('#lieu').append(adresse);
                }


            },
            error : function(xhr, textStatus, errorThrown) {
                alert("Impossible de trouver l'adresse.");
            }
        });
    });


}
