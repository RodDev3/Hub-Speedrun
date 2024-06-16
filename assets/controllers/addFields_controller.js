import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";

export default class extends Controller {
    connect() {


        this.addFieldsButtons = this.element.querySelectorAll(".addFieldsButton");
        this.deleteFieldsButtons = this.element.querySelectorAll(".deleteFieldButtons");

        let numberClicks = 0;
        this.newFields = this.element.querySelector("#newFields");

        this.deleteFieldsButtons.forEach((button) => {
            this.delete(button)
        })

        this.addFieldsButtons.forEach((button) => {
            button.addEventListener("click", this.add.bind(this));
        });
    }

    //Delete field
    delete (button){
        button.addEventListener('click', (e)=> {
            e.preventDefault();

            let fieldWrapper = button.parentElement.parentElement;
            fieldWrapper.remove();
        })
    }

    //Add field
    async add(e){
            e.preventDefault();

            //loader
            this.newFields.insertAdjacentHTML("beforeend", "<div id=\"loader\" class=\"mt-4 d-flex justify-content-center\"> <div class=\"spinner-border text-white\" role=\"status\">" +
            "<span class=\"visually-hidden\">Loading...</span>" +
            "</div></div>");

            let select = document.querySelector("#addFields");


            let response = await fetch('/categories/call/addField', {
                method: 'POST',
                body: JSON.stringify({'type' : select.value})
            })

            let data = await response.json();

            if (response.status === 200){
                document.querySelector('#loader').remove();
                this.newFields.insertAdjacentHTML("beforeend", data);
            }else if(response.status === 400){
                document.querySelector('#loader').remove();
                toastr.error(data.message,'Error');
            }

            this.deleteFieldsButtons = this.element.querySelectorAll(".deleteFieldButtons");
            this.deleteFieldsButtons.forEach((button) => {
                this.delete(button)
            })
    }
}