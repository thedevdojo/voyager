/**********************************************************************
 *  
 *  Global UI Functionality for Voyager
 * 
 *  This is custom js functionality for the Voyager UI may include
 *  functionality for dropdowns, modals, textbox, etc...
 * 
 **********************************************************************/

 /*
 * VOYAGER TEXT BOX
 *
 * This functionality is for a textbox container using the .voyager-input-container class
 */

var toggleVoyagerInputContainer = function (input){
    if(input.value != ""){
        input.classList.add('voyager-filled');
    } else {
        input.classList.remove('voyager-filled');
    }
}

window.addEventListener("load", function(){
    var voyagerInputs = document.getElementsByClassName("voyager-input");
    for(var i=0; i < voyagerInputs.length; i++){
        console.log('looped');
        voyagerInputs[i].addEventListener('keyup', function (){
            console.log('tgle');

            toggleVoyagerInputContainer(this);
        });
        toggleVoyagerInputContainer(voyagerInputs[i]);
    }
});

