import { Controller } from '@hotwired/stimulus';
import TomSelect from "tom-select";

export default class extends Controller {

    ajaxUrl = "call/user";
    static values = {
        rewrite: String
    }


    connect() {
        let form = this.element.querySelector('#manageUsersModeration');
        let userSelect = this.element.querySelector('#moderations_refUsers');

        new TomSelect('#moderations_refUsers', {});


        userSelect.tomselect.on('change', (e) => {
           this.fetchData(new FormData(form));
        });
    }




    async fetchData(data) {

        console.log(this.ajaxUrl);
        let ajax = await fetch(this.ajaxUrl, {
            body: data,
            method: "POST"
        })

        let json;
        try {
            json = await ajax.json();
        } catch (e) {
            alert("Les données récupérées sont éronnées")
            return;
        }

        if (ajax.status !== 200) {
            alert(json);
            return;

        }



        // Fait ce que tu veux ici

        //adjacentHtml


    }
}