import {Controller} from "@hotwired/stimulus";
import toastr from "toastr";


export default class extends Controller {
    connect() {

        this.leaderboard = document.querySelector('#leaderboard');
        this.categories = this.element.querySelectorAll(".categories");
        this.gameRewrite = window.location.href.split('/').slice(-1);

        this.categories.forEach((category) =>{
            category.addEventListener('click', (e) => {
                this.applyCategories(e, category)
            });
        })

    }

    async applyCategories(e, category){
        e.preventDefault()

        let response = await fetch('/game/call/categories', {
            method : 'POST',
            body : JSON.stringify({
                'categories' : category.getAttribute('data-categories'),
                'games' : this.gameRewrite[0]
            })
        })

        let data = await response.json()
        if (response.status === 200){
            console.log(data)
            this.leaderboard.innerHTML = data;
        }else{
            toastr.error(data.message, 'Error')
        }

    }
}
