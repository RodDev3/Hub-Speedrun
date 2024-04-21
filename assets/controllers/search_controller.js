import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    debounce(callback, delay){
        let timer;
        return function(){
            const args = arguments;
            const context = this;
            clearTimeout(timer);
            timer = setTimeout(function(){
                callback.apply(context, args);
            }, delay)
        }
    }

    connect() {

        let form = document.querySelector("#formSearch")
        let input = document.querySelector('#search_search');

        input.addEventListener('input' , this.debounce(async function (e) {

            //Set formdata
            let formData = new FormData(form);

            try{

                //Ajax call
                const response = fetch('/research/call', {
                    method: 'POST',
                    body: formData
                })

                const data = await response.json();

                console.log(data)

            }catch (error){
                console.error('Ajax error :' + error)
            }

        }, 500))
    }
}
