function formhash(form, password) {
	
    // Create a new element input, this will be our hashed password field. 
    var p = document.createElement("input");
 
    // Add the new element to our form. 
    form.appendChild(p); 
    p.name = "p";
    p.type = "hidden";
    p.value = sha512(password.value);
    // Make sure the plaintext password doesn't get sent. 
    password.value = "";
 
    // Finally submit the form. 
	
    form.submit();
}