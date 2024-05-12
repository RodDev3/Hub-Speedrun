import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";

export default class extends Controller {
    connect() {

        let buttons = document.querySelectorAll(".addFieldsButton");

        let numberClicks = 0;
        let newFields = document.querySelector("#newFields");

        buttons.forEach((button) => {
            button.addEventListener("click", async (e) => {
                e.preventDefault();

                let select = document.querySelector("#addFields");

                let response = await fetch('/categories/call/addField', {
                    method: 'POST',
                    body: JSON.stringify({'type' : select.value})
                })

                let data = await response.json();

                if (response.status === 200){
                    newFields.insertAdjacentHTML("beforeend", data);
                }else if(response.status === 400){
                    toastr.error(data.message,'Error');
                }

            });
        });
    }
}