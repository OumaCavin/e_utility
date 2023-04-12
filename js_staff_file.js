//Staff_jsfile.js
const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

sign_up_btn.addEventListener("click", () => {
  container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
});

function checkValid() {
  let mail = document.getElementById("emailid").value;
  let format = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/.test(mail);
  if (!format)
   document.getElementById("mail").innerHTML = "Enter a valid Kenya Power email id!";
  else{
  const myForm = document.getElementById("registration-form");
  document.querySelector(".btn").addEventListener("click", function(){

  myForm.submit();
  });
  }
}