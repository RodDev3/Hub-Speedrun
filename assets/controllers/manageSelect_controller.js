import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";

export default class extends Controller {

    static values = {
        uuid: String
    }

    connect() {

        this.pointer = 2;

        this.deleteButtons = document.querySelectorAll(".deleteButtonSelect");

        this.addButtons = document.querySelectorAll(".addButtons");

        this.newOptions = document.querySelector("#newOptions" + this.uuidValue);

        this.deleteButtons.forEach((button) => {
            this.delete(button);
        });

        this.addButtons.forEach((button) => {
            button.addEventListener("click", this.add.bind(this));
        })
    }

    delete(button){
        button.addEventListener('click', (e)=> {
            e.preventDefault();

            let parent = button.parentElement;
            parent.remove();
        })
    }

    add(e){
        e.preventDefault();

        this.pointer += 1;

        this.newOptions.insertAdjacentHTML("beforeend", "<div class='input-group mb-3'>" +
            "<input type='text' class='form-control' name='categories[fields][select." + this.uuidValue + "][option." + this.pointer + "]' placeholder='Option' aria-label=''>" +
            "<button class='btn btn-outline-secondary deleteButtonSelect' type='button' id=''>Delete</button>" +
            "</div>"
        );

        //set les nouveaux deleteButtons et leur ajoute l'eventListener
        let newDeleteButtons = document.querySelectorAll(".deleteButtonSelect");
        newDeleteButtons.forEach((button) => {
            this.delete(button)
        })
    }
}