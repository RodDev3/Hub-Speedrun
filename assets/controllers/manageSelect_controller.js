import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";

export default class extends Controller {

    static values = {
        uuid: String
    }

    connect() {

        //Pointer
        this.pointer = 2;

        this.deleteButtons = this.element.querySelectorAll(".deleteButtonSelect");
        this.addButton = this.element.querySelector("#addButton");
        this.newOptions = this.element.querySelector("#newOptions");


        this.deleteButtons.forEach((button) => {
            this.delete(button);
        });

        this.addButton.addEventListener("click", this.add.bind(this));
    }

    //Function delete option
    delete(button){
        button.addEventListener('click', (e)=> {
            e.preventDefault();

            let parent = button.parentElement;
            parent.remove();
        })
    }

    //Function add option
    add(e){
        e.preventDefault();

        this.pointer += 1;

        this.newOptions.insertAdjacentHTML("beforeend", "<div class='input-group mb-3'>" +
            "<input type='text' class='form-control' name='categories[fields][select." + this.uuidValue + "][option." + this.pointer + "]' placeholder='Option' aria-label=''>" +
            "<button class='btn btn-outline-secondary deleteButtonSelect' type='button' id=''>Delete</button>" +
            "</div>"
        );

        //set les nouveaux deleteButtons et leur ajoute l'eventListener
        this.deleteButtons = this.element.querySelectorAll(".deleteButtonSelect");
        this.deleteButtons.forEach((button) => {
            this.delete(button)
        })
    }
}