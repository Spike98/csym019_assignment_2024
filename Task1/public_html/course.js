const exchangeRateKey = "43a9aa45d3aa15a5be06802e";
const exchangeRateUrl = `https://v6.exchangerate-api.com/v6/43a9aa45d3aa15a5be06802e/latest/USD`;

function fetchAndUpdate() {  //fetch data from course.json with ajax
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "course.json", true);

    xhr.onload = function () {  //using AJAX to parse the json file
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            console.log(`Total courses displayed: ${data.courses.length}`); // helps me check some errors that occured while writting the code
            fetchExchangeRate(data.courses);
        }else{
            console.error("Failed to load data: ",xhr.status,xhr.statusText);
        }
    };
    
    xhr.onerror = function () {
        console.error("Connection Error");
    };
    
    xhr.send(); //send request to server
}

function fetchExchangeRate(courses){
    const xhr = new XMLHttpRequest();
    xhr.open("GET", exchangeRateUrl, true);
    
    xhr.onload = function () {
        if (xhr.status === 200){
            const response = JSON.parse(xhr.responseText);
            console.log("Full API Call: ", response);
            const rate = response.conversion_rates; //conversion_rates == API key holding the exchange rates!
            if(rate && rate.USD && rate.EUR && rate.GBP){
                console.log("Fetched ex.rates: ",rate);
                updateTable(courses, rate);
            }else{
                console.error("Required ex.rates (USD,EUR,GBP) not defined.");
            }
        }else {
            console.error ("Failed to load ex.rates: ",xhr.status,xhr.statusText);
        }
    };
    
    xhr.onerror= function(){
        console.error("Connection Failed");
    };
    xhr.send();
}

function updateTable(courses, rate) {
    const tbody = document.querySelector(".container tbody"); //clear table's content before calling new content
    tbody.innerHTML = "";
    
    const currencies = ["USD", "EUR", "GBP"];
    
    courses.forEach((course,index) => {
        try {
            const row = document.createElement("tr");

            //icons
            const iconSpace = document.createElement("td");
            const img = document.createElement("img");
            img.src = course.icon;
            img.alt = course.title + " photo";
            iconSpace.appendChild(img);
            row.appendChild(iconSpace);

            //title
            const titleSpace = document.createElement("td");
            titleSpace.textContent = course.title;
            row.appendChild(titleSpace);

            //level
            const levelSpace = document.createElement("td");
            levelSpace.textContent = course.level;
            row.appendChild(levelSpace);

            //overview
            const overviewSpace = document.createElement("td");
            overviewSpace.textContent = course.overview;
            row.appendChild(overviewSpace);

            //highlights
            const highlightsSpace = document.createElement("td");
            highlightsSpace.textContent = course.highlights.join('.\n\n');
            row.appendChild(highlightsSpace);

            //modules
            const modulesSpace = document.createElement("td");
            modulesSpace.textContent = course.modules.join(".\n\n");
            row.appendChild(modulesSpace);

            //entry requirements
            const reqSpace = document.createElement("td");
            reqSpace.textContent = course.entry_requirements.join('.\n\n');
            row.appendChild(reqSpace);

            //fees
            const feesSpace = document.createElement("td");
            const feesDetail = [];
            for (const[category, amountStr] of Object.entries(course.fees_funding)){  // array of key/value pairs for object fees_funding & iterate over each pair with 'for..of'
                const amount = parseFloat(amountStr.replace(/[^0-9.-]+/g,""));        // remove non-numeric char from amountStr which contains the fee amount as a string
                const diffCurrency = currencies.map(currency =>{
                    const amountConverted = (amount * rate[currency]).toFixed(2);
                    return `${amountConverted} ${currency}`;
                }).join(" / ");
                feesDetail.push(`${category}: ${diffCurrency}`);
            }
            feesSpace.textContent = feesDetail.join("\n\n");
            row.appendChild(feesSpace);
            
            //staff
            const staffSpace = document.createElement("td");
            staffSpace.textContent = course.staff.join('\n\n');
            row.appendChild(staffSpace);

            //careers
            const careersSpace = document.createElement("td");
            careersSpace.textContent = course.careers_employability;
            row.appendChild(careersSpace);

            //facilities
            const facilitiesSpace = document.createElement("td");
            facilitiesSpace.textContent = course.facilities.join(', ');
            row.appendChild(facilitiesSpace);

            //related courses
            const relatedSpace = document.createElement("td");
            relatedSpace.textContent = Object.keys(course.relatedCourses).join(', ');
            row.appendChild(relatedSpace);

            tbody.appendChild(row);
            
            console.log(`Course ${index + 1}: ${course.title} added.`);
        }catch (error){
            console.error(`Error calling course ${index+1}: ${course.title}`, error);
        }
    });

}
fetchAndUpdate();

setInterval(fetchAndUpdate, 150000);