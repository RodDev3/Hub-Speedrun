import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    connect() {

        let form = document.querySelector("#formRuns");
        let category = document.querySelector('#runs_refCategories');
        let loadFields = document.querySelector('#loadFields');


        //TODO ajout d'un event au chargement du dom pour charger les fields
        category.addEventListener('change', async function (e){

            //TODO découper la page en grande catégorie comme Players / Times / Video / Others"
            loadFields.innerHTML = "";
            e.preventDefault();
            let formData = new FormData(form);

            let response = await fetch('/runs/fields/call',{
                method: 'POST',
                body: formData
            })

            let data = await response.json();
            if (response.status === 200){

                data.forEach((fields) => {
                    console.log(fields)
                        switch (fields.type){
                            case 'time-goal':
                                    loadFields.insertAdjacentHTML('beforeend', '<div>' +
                                        '<div>' + fields.label + '</div>' +
                                            '<div class="input-group mb-3">' +
                                                '<input type="text" class="form-control" name="run[' + fields.id + '][hours]"' +
                                                    (`mandatory` in fields ? "required='required'" : "")
                                                    + ' maxlength="3" placeholder="" aria-label="">' +
                                                '<span class="input-group-text">h</span>' +

                                                '<input type="text" class="form-control" name="run[' + fields.id + '][minutes]"' +
                                                    (`mandatory` in fields ? "required='required'" : "") +
                                                    ' maxlength="2" placeholder="" aria-label="">' +
                                                '<span class="input-group-text">m</span>' +

                                                '<input type="text" class="form-control" name="run[' + fields.id + '][secondes]"' +
                                                    (`mandatory` in fields ? "required='required'" : "") +
                                                    ' maxlength="2" placeholder="" aria-label="">' +
                                                '<span class="input-group-text">s</span>' +

                                                '<input type="text" class="form-control" name="run[' + fields.id + '][milliseconds]"' +
                                                    (`mandatory` in fields ? "required='required'" : "") +
                                                    ' maxlength="3" placeholder="" aria-label="">' +
                                                '<span class="input-group-text">ms</span>' +

                                            '</div>' +
                                        '</div>'
                                    );
                                break;
                        }
                    /*for (let field in fields){
                        console.log(field)
                    }*/
                })
            } else if (response.status === 400){
                toastr.error(data.message,'Error')
            }

            //Switch sur ce que je renvoie pour les afficher, affiché en 1er le primary et si on secondary existe l'affiche ensuite et les autres pour les goals


        })
    }
}
