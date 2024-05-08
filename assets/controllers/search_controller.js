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
                const response = await fetch('/research/call', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok === true){
                    const data = await response.json();
                    /*console.log(data)
                    console.log(data['games'][0].name)*/

                    let resultsDiv = document.querySelector('#searchResults');

                    resultsDiv.innerHTML = "";

                    if (data.games.length > 0) {
                        resultsDiv.innerHTML += "<p>Games</p>";
                    }
                    for (const result in data.games){
                        resultsDiv.innerHTML += "<a href='/games/"+data.games[result].rewrite +"'>" +
                            "<p>"+ data.games[result].name +"</p>" +
                            "<p>"+ data.games[result].image +"</p>" +
                            "<p>"+ data.games[result].rewrite +"</p>" +
                            "<p>"+ data.games[result].releaseDate +"</p>" +
                            "</a>"
                    }

                    if (data.players.length > 0) {
                        resultsDiv.innerHTML += "<p>Players</p>";
                    }
                    for (const result in data.players){
                        resultsDiv.innerHTML += "<a href=''>" +
                            "<p>"+ data.players[result].username +"</p>" +
                            "</a>"
                    }

                }else{
                    console.error('Status error')
                }

            }catch (error){
                console.error('Ajax error :' + error)
            }

        }, 500))
    }
}
