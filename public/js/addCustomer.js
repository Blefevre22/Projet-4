 $(document).ready(function() {
        // On récupère la balise <div> en question qui contient l'attribut « data-prototype » qui nous intéresse.
        var $container = $('div#booking_customer');

        // On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
        var index = $container.find(':input').length;

        // On ajoute un nouveau champ à chaque clic sur le lien d'ajout.
        $('#add_ticket').on('click',function(e) {
            //Si le tarif journée de la première réservation est caché
            if( $('.radio').eq(0).css('display')== 'none'){
                //Ajout d'un ticket
                addTicket($container);
                //Récupération du nombre de input radio
                var number = $('.radio').length - 2;
                //Dissimulation du ticket journée du dernier ajout
                $('.radio').eq(number).hide()
            }else{
                addTicket($container);
            }
            $('#add_ticket').insertBefore($('.btn-danger').last())

            e.preventDefault(); // évite qu'un # apparaisse dans l'URL
            return false;
        });

        // On ajoute un premier champ automatiquement s'il n'en existe pas déjà un (cas d'une nouvelle annonce par exemple).
        if (index == 0) {
            addTicket($container);
        } else {
            // S'il existe déjà des catégories, on ajoute un lien de suppression pour chacune d'entre elles
            $container.children('div').each(function() {
                addDeleteTicket($(this));
            });
        }

        // La fonction qui ajoute un formulaire CategoryType
        function addTicket($container) {
            // Dans le contenu de l'attribut « data-prototype », on remplace :
            // - le texte "__name__label__" qu'il contient par le label du champ
            // - le texte "__name__" qu'il contient par le numéro du champ
            var template = $container.attr('data-prototype')
                .replace(/__name__label__/g, 'Billet n°' + (index+1))
                .replace(/__name__/g,        index)
            ;

            // On crée un objet jquery qui contient ce template
            var $prototype = $(template);

            // On ajoute au prototype un lien pour pouvoir supprimer la catégorie
            addDeleteTicket($prototype);

            // On ajoute le prototype modifié à la fin de la balise <div>
            $container.append($prototype);

            // Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
            index++;
        }

        // La fonction qui ajoute un lien de suppression d'une catégorie
        function addDeleteTicket($prototype) {
            // Création du lien
            var $deleteTicket = $('<a href="#" class="btn btn-danger">Supprimer</a>');

            // Ajout du lien
            $prototype.append($deleteTicket);

            // Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
            $deleteTicket.click(function(e) {
                $prototype.remove();

                e.preventDefault(); // évite qu'un # apparaisse dans l'URL
                return false;
            });
        }
 });