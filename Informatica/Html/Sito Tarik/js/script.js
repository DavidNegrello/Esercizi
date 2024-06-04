const body = document.querySelector("body");

// smooth scroll

document.querySelectorAll(".scroll-link").forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    document.querySelector(this.getAttribute("href")).scrollIntoView({
      behavior: "smooth",
    });
  });
});

// // popup kebab

const productCards = document.querySelectorAll(".card-menu");
const productPopup = document.querySelector(".popupBuy");
const productPopupWrapper = document.querySelector(".popupBuy__wrapper");
const blackBg = document.querySelector(".main__black");
const popupLoginClose = document.querySelector(".popupBuy__close-btn");


productCards.forEach((product) => {
  product.addEventListener("click", () => {
    productPopup.style.display = "block";
    productPopupWrapper.style.zIndex = "3";
    blackBg.style.display = "block";
    body.style.overflow = "hidden";
  });
});

popupLoginClose.addEventListener("click", () => {
  productPopup.style.display = "none";
  productPopupWrapper.style.zIndex = "0";
  blackBg.style.display = "none";
  body.style.overflow = "visible";
});



// popup login

const loginBtn = document.querySelector(".header__login-link");
const loginPopup = document.querySelector(".popup-login");
const loginPopupBtn = document.querySelector(".popup-login__button");
      const loginPopupWrapper = document.querySelector(
        ".popupBuy__wrapper_login"
      );

const popupLoginCloseBtn = document.querySelector(".popup-login__close-btn");

loginBtn.addEventListener("click", (e) => {
  e.preventDefault();
  const productPopupCloseBtn = document.querySelector(".popupBuy__close-btn");
  loginPopupWrapper.style.display = "flex";
  loginPopup.style.display = "flex";
  body.style.overflow = "hidden";
  loginPopupWrapper.style.zIndex = "3";
  productPopupCloseBtn.addEventListener("click", () => {
    loginPopup.style.display = "none";
    body.style.overflow = "visible";
    loginPopupWrapper.style.display = "none";
  });
  loginPopupBtn.addEventListener("click", () => {
    const loginResult = document.querySelector(".popup-login__result");
    const userName = document.getElementById("username");
    const password = document.getElementById("password");
    localStorage.setItem("username", userName.value);
    localStorage.setItem("password", password.value);
    loginResult.innerHTML = "You're logged in!";
    setTimeout(() => {
      loginPopupWrapper.style.display = "none";
      loginPopup.style.display = "none";
      body.style.overflow = "visible";
      userName.value = "";
      password.value = "";
      loginResult.innerHTML = "";
    }, 3000);
  });
});

popupLoginCloseBtn.addEventListener("click", () => {
  loginPopupWrapper.style.display = "none";
  loginPopup.style.display = "none";
  body.style.overflow = "visible";
});

