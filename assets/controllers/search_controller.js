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

            //TODO NOTION pour console log un formdata console.log(Object.fromEntries(formData));


            let resultsDiv = document.querySelector('#searchResults');
            resultsDiv.classList.remove('visually-hidden');

            resultsDiv.innerHTML = '<div id="loader" class="d-flex justify-content-center"> <div class="spinner-border text-white" role="status">' +
                '<span class="visually-hidden">Loading...</span>' +
                '</div></div>';

            try{

                //Ajax call
                const response = await fetch('/research/call', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok === true){
                    const data = await response.json();


                    if (data.games === ""){
                        //If no result hide results div
                        resultsDiv.classList.add('visually-hidden');
                    }else{
                        //Show Games results
                        resultsDiv.innerHTML = data.games;
                    }

                    /*if (data.players.length > 0) {
                        resultsDiv.innerHTML += "<p>Players</p>";
                    }
                    for (const result in data.players){
                        resultsDiv.innerHTML += "<a href=''>" +
                            "<p>"+ data.players[result].username +"</p>" +
                            "</a>"
                    }*/

                }else{
                    console.error('Status error')
                }

            }catch (error){
                console.error('Ajax error :' + error)
            }

        }, 500))
    }
}
