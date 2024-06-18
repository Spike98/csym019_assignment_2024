
function fetchAndUpdate(){  //fetch data from course.json with ajax
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'course.json', true);
    
    xhr.onload = function(){
        if(xhr.status === 200){ 
            const data = JSON.parse(xhr.responseText); 
            updateTable(data.courses);  
        }
    };
    xhr.send(); //send request to server
}

function updateTable(courses){
    const tableBody = document.querySelector("#course-table tableBody"); //clear table's content before calling new content
    tableBody.innerHTML = "";
    
    courses.forEach(course =>{
        const row = document.createElement("tr");
        
        //icons
        const icon = document.createElement("td");
        const img = document.createElement("img");
        img.src = course.icon;
        
    })
    
}