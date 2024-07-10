//δημιουργια δυο κελιων για προσθηκη ονομασιας και credits καθε module
function addModule() {
    const moduleDiv = document.createElement('div');
    moduleDiv.className = 'module';

    const moduleNameInput = document.createElement('input');
    moduleNameInput.type = 'text';
    moduleNameInput.name = 'module_name[]';
    moduleNameInput.placeholder = 'Module Name';
    moduleNameInput.required = true;

    const moduleCreditsInput = document.createElement('input');
    moduleCreditsInput.type = 'number';
    moduleCreditsInput.name = 'module_credits[]';
    moduleCreditsInput.placeholder = 'Credits';
    moduleCreditsInput.required = true;

    moduleDiv.appendChild(moduleNameInput);
    moduleDiv.appendChild(moduleCreditsInput);

    document.getElementById('modules').appendChild(moduleDiv);
}
//αφαιρει το τελευταίο div των modules
function removeModule() {
    const modulesDiv = document.getElementById('modules');
    if (modulesDiv.children.length > 0) {
        modulesDiv.removeChild(modulesDiv.lastChild);
    }
}



