import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
    connect() {

        let buttons = document.querySelectorAll(".addFieldsButton");

        let numberClicks = 0;

        buttons.forEach((button) => {
            button.addEventListener("click", () => {

                numberClicks += 1;

                let select = document.querySelector("#addFields" + button.dataset.fields);
                let newFields = document.querySelector("#newFields" + button.dataset.fields);

                switch (select.value) {
                    //TODO PRENDRE LE NOM DU CHAMP QUAND IL EST REMPLI POUR LE METTRE DANS LE NAME
                    case "select":
                        newFields.insertAdjacentHTML("beforebegin", "<input type='text'/>" +
                            "<select name='' id=''></select>" +
                            "");
                        break;
                    case "date":
                        newFields.insertAdjacentHTML("beforebegin", "<input type='date'/>");
                        break;
                    case "text":
                        newFields.insertAdjacentHTML("beforebegin", "<input type='text'/>");
                        break;

                    case "time-goal":
                        //TODO RECUPERER LE TIMING DEFAULT ET SECONDAIRE SI IL EXISTE POUR FAIRE LE LEADERBOARD (A METTRE DANS LA CATEGORIE (defaultField = Nom du champ, secondary field = nom du champ / null) )
                        newFields.insertAdjacentHTML("beforebegin", "<div>" +
                                "<div class='input-group mb-3'>" +
                                    "<input type='text' class='form-control' name='categories[fields][time-goal." + numberClicks + "][label]' placeholder='Title' aria-label='Title'>" +
                                "</div>" +
                                "<div class='input-group mb-3'>" +
                                    "<input type='text' class='form-control' name='categories[fields][time-goal." + numberClicks + "][hours]' placeholder='' aria-label='' disabled>" +
                                    "<span class='input-group-text'>h</span>" +
                                    "<input type='text' class='form-control' name='categories[fields][time-goal." + numberClicks + "][minutes]' placeholder='' aria-label='' disabled>" +
                                    "<span class='input-group-text'>m</span>" +
                                    "<input type='text' class='form-control' name='categories[fields][time-goal." + numberClicks + "][seconds]' placeholder='' aria-label='' disabled>" +
                                    "<span class='input-group-text'>s</span>" +
                                    "<input type='text' class='form-control' name='categories[fields][time-goal." + numberClicks + "][millisecond]' placeholder='' aria-label='' disabled>" +
                                    "<span class='input-group-text'>ms</span>" +
                                "</div>" +
                                "<div class='form-check form-switch'>" +
                                    "<input class='form-check-input checkboxValue' name='categories[fields][time-goal." + numberClicks + "][primary]' type='checkbox' role='switch' id=''>" +
                                    "<label class='form-check-label' for='flexSwitchCheckDefault'>Timing value by default</label>" +
                                "</div>" +
                                "<div class='form-check form-switch'>" +
                                    "<input class='form-check-input' name='categories[fields][time-goal." + numberClicks + "][secondary]' type='checkbox' role='switch' id=''>" +
                                    "<label class='form-check-label' for='flexSwitchCheckDefault'>Timing value secondary</label>" +
                                "</div>" +
                                "<div class='form-check form-switch'>" +
                                    "<input class='form-check-input' name='categories[fields][time-goal." + numberClicks + "][mandatory]' type='checkbox' role='switch' id=''>" +
                                    "<label class='form-check-label' for='flexSwitchCheckDefault'>Mandatory</label>" +
                                "</div>" +
                            "</div>"
                        );
                        break;

                }

            });
        });
    }
}