var ace_editor_element = document.getElementsByClassName("ace_editor");

// For each ace editor element on the page
for(var i = 0; i < ace_editor_element.length; i++)
{

	// Create an ace editor instance
	var ace_editor = ace.edit(ace_editor_element[i].id);

	// Get the corresponding text area associated with the ace editor
	var ace_editor_textarea = document.getElementById(ace_editor_element[i].id + '_textarea');

    if(ace_editor_element[i].getAttribute('data-theme')){
    	ace_editor.setTheme("ace/theme/" + ace_editor_element[i].getAttribute('data-theme'));
    }

    if(ace_editor_element[i].getAttribute('data-language')){
    	ace_editor.getSession().setMode("ace/mode/" + ace_editor_element[i].getAttribute('data-language'));
    }
    
    ace_editor.on('change', function(event, el) {
    	ace_editor_id = el.container.id;
    	ace_editor_textarea = document.getElementById(ace_editor_id + '_textarea');
    	ace_editor_instance = ace.edit(ace_editor_id);
    	ace_editor_textarea.value = ace_editor_instance.getValue();
    });
}
