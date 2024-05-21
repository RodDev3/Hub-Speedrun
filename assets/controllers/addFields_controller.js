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

            let select = document.querySelector("#addFields");

            let response = await fetch('/categories/call/addField', {
                method: 'POST',
                body: JSON.stringify({'type' : select.value})
            })

            let data = await response.json();

            if (response.status === 200){
                this.newFields.insertAdjacentHTML("beforeend", data);
            }else if(response.status === 400){
                toastr.error(data.message,'Error');
            }

            this.deleteFieldsButtons = this.element.querySelectorAll(".deleteFieldButtons");
            this.deleteFieldsButtons.forEach((button) => {
                this.delete(button)
            })
    }
}