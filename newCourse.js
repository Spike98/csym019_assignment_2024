//δημιουργια δυο κελιων για προσθηκη ονομασιας και credits καθε module
function addModule() {
    var moduleDiv = document.createElement("div");
    moduleDiv.classList.add("module");

    var moduleNameLabel = document.createElement("label");
    moduleNameLabel.textContent = "Module Name:";
    moduleDiv.appendChild(moduleNameLabel);

    var moduleNameInput = document.createElement("input");
    moduleNameInput.type = "text";
    moduleNameInput.name = "module_name[]";
    moduleNameInput.required = true;
    moduleDiv.appendChild(moduleNameInput);

    var moduleCreditsLabel = document.createElement("label");
    moduleCreditsLabel.textContent = "Credits:";
    moduleDiv.appendChild(moduleCreditsLabel);

    var moduleCreditsInput = document.createElement("input");
    moduleCreditsInput.type = "number";
    moduleCreditsInput.name = "module_credits[]";
    moduleCreditsInput.required = true;
    moduleDiv.appendChild(moduleCreditsInput);

    document.getElementById("modules").appendChild(moduleDiv);
}

function removeModule() {
    var modulesDiv = document.getElementById("modules");
    if (modulesDiv.children.length > 0) {
        modulesDiv.removeChild(modulesDiv.lastChild);
    }
}




