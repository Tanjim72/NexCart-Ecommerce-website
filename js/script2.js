function validateProductForm(){
    let name = document.querySelector('[name="name"]').value.trim();
    let category = document.querySelector('[name="category"]').value.trim();
    let brand = document.querySelector('[name="brand"]').value.trim();
    let price = document.querySelector('[name="price"]').value.trim();
    let rating = document.querySelector('[name="rating"]').value.trim();

    if(!name || !category || !brand || !price || !rating){
        alert("All fields are required!");
        return false;
    }
    if(isNaN(price) || isNaN(rating)){
        alert("Price and Rating must be numeric values!");
        return false;
    }
    if(parseFloat(price)<0){
        alert("Price cannot be negative!");
        return false;
    }
    if(parseFloat(rating)<0 || parseFloat(rating)>5){
        alert("Rating must be between 0 and 5!");
        return false;
    }
    return true;
}
