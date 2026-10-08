<script>
  function loadWonderchatScript() {
    const child = document.getElementById(
      "placeholder-wonderchat-button"
    );
    const parent = child.parentNode;
    parent.removeChild(child);
    
    var wcs = document.createElement("script")
    wcs.setAttribute("data-name", "wonderchat");
    wcs.setAttribute("data-address", "app.wonderchat.io");
    wcs.setAttribute("data-id", "clyqayrnl0152jk6ql3343c0e");
    wcs.setAttribute("src", "https://app.wonderchat.io/scripts/wonderchat.js");
    wcs.setAttribute("data-widget-size", "normal");
    wcs.setAttribute("data-widget-button-size", "normal");
    wcs.setAttribute("data-initial-open", "true");
    document.body.appendChild(wcs);
  
  }
  function insertWonderChatButton() {
    const buttonHTML = `
<div style="position: fixed; bottom: 0; right: 6px">
  <button
    class="icon-button"
    id="placeholder-wonderchat-button"
    aria-label="Open WonderChat"
    style="
      margin: 0 18px 18px 18px;
      border: none;
      border-radius: 99px;
      width: calc(60px * 0.9); 
      height: calc(60px * 0.9); 
      background-color: #265677;
      margin-top: 12px;
      display: flex;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 250ms cubic-bezier(0.33, 0, 0, 1);
      position: relative; /* Needed for absolute positioning of icon */
      align-items: center;
      -webkit-box-pack: center;
      justify-content: center;
      user-select: none;
      position: relative;
      vertical-align: middle;
      outline-offset: 2px;
      line-height: 1.2;
      cursor: pointer;
    "
    onmouseover="this.style.transform='scale(1.05)'"
    onmouseout="this.style.transform='scale(1)'"
    onmousedown="this.style.transform='scale(0.85)';"
    onmouseup="this.style.transform='scale(1.05)';"
    onclick="loadWonderchatScript()"
  >
    <svg
      style="
        display: inline-block;
        line-height: 1em;
        flex-shrink: 0;
        color: currentcolor;
        vertical-align: middle;
        fill: none;
        height: calc(37px * 0.9);
        width: calc(34px * 0.9);
        position: absolute;
        opacity: 1;
        transition: transform 0.16s linear 0s, opacity 0.08s linear 0s;
        transform: rotate(0deg);
      "
      viewBox="0 0 37 37"
      focusable="false"
      class="chakra-icon css-sqob30"
      xmlns="http://www.w3.org/2000/svg"
    >
      <g fill="#FFFCFD" filter="url(#a)">
        <path
          d="m22.25 21.78 5.99 10.106c1.448 2.441 5.218 1.423 5.218-1.41v-8.695H22.25Z"
        ></path>
        <path
          d="M36.26 13.44C36.26 6.379 30.49.653 23.37.653H13.282C6.162.652.392 6.378.392 13.441v1.112c0 7.063 5.77 12.788 12.89 12.788H23.37c7.12 0 12.89-5.726 12.89-12.788V13.44Z"
        ></path>
      </g>
      <g filter="url(#b)">
        <path
          fill="url(#c)"
          d="M25.561 10.543a.557.557 0 0 0-1.02 0l-.858 1.945a.559.559 0 0 1-.285.285l-1.938.86a.56.56 0 0 0 0 1.024l1.938.861a.559.559 0 0 1 .285.285l.858 1.945a.557.557 0 0 0 1.02 0l.859-1.945a.559.559 0 0 1 .284-.285l1.938-.86a.56.56 0 0 0 0-1.024l-1.938-.861a.559.559 0 0 1-.284-.285l-.859-1.945Z"
        ></path>
        <path
          fill="url(#d)"
          d="M14.402 14.146a2.245 2.245 0 0 0 2.242 2.248 2.245 2.245 0 0 0 2.242-2.248 2.245 2.245 0 0 0-2.242-2.25 2.245 2.245 0 0 0-2.242 2.25Z"
        ></path>
        <path
          fill="url(#e)"
          d="M7.677 14.146a2.245 2.245 0 0 0 2.242 2.248 2.245 2.245 0 0 0 2.242-2.248 2.245 2.245 0 0 0-2.242-2.25 2.245 2.245 0 0 0-2.242 2.25Z"
        ></path>
      </g>
      <defs>
        <linearGradient
          id="c"
          x1="18.326"
          x2="18.326"
          y1="10.21"
          y2="16.652"
          gradientUnits="userSpaceOnUse"
        >
          <stop></stop>
          <stop offset="1"></stop>
        </linearGradient>
        <linearGradient
          id="d"
          x1="18.326"
          x2="18.326"
          y1="10.21"
          y2="16.652"
          gradientUnits="userSpaceOnUse"
        >
          <stop></stop>
          <stop offset="1"></stop>
        </linearGradient>
        <linearGradient
          id="e"
          x1="18.326"
          x2="18.326"
          y1="10.21"
          y2="16.652"
          gradientUnits="userSpaceOnUse"
        >
          <stop></stop>
          <stop offset="1"></stop>
        </linearGradient>
        <filter
          id="a"
          width="35.869"
          height="35.609"
          x="0.392"
          y="0.652"
          color-interpolation-filters="sRGB"
          filterUnits="userSpaceOnUse"
        >
          <feFlood
            flood-opacity="0"
            result="BackgroundImageFix"
          ></feFlood>
          <feBlend
            in="SourceGraphic"
            in2="BackgroundImageFix"
            result="shape"
          ></feBlend>
          <feColorMatrix
            in="SourceAlpha"
            result="hardAlpha"
            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
          ></feColorMatrix>
          <feOffset dy="3"></feOffset>
          <feGaussianBlur stdDeviation="2"></feGaussianBlur>
          <feComposite
            in2="hardAlpha"
            k2="-1"
            k3="1"
            operator="arithmetic"
          ></feComposite>
          <feColorMatrix
            values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.25 0"
          ></feColorMatrix>
          <feBlend
            in2="shape"
            result="effect1_innerShadow_66_6341"
          ></feBlend>
        </filter>
        <filter
          id="b"
          width="21.298"
          height="10.871"
          x="7.677"
          y="10.21"
          color-interpolation-filters="sRGB"
          filterUnits="userSpaceOnUse"
        >
          <feFlood
            flood-opacity="0"
            result="BackgroundImageFix"
          ></feFlood>
          <feBlend
            in="SourceGraphic"
            in2="BackgroundImageFix"
            result="shape"
          ></feBlend>
          <feColorMatrix
            in="SourceAlpha"
            result="hardAlpha"
            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
          ></feColorMatrix>
          <feOffset dy="3"></feOffset>
          <feGaussianBlur stdDeviation="2"></feGaussianBlur>
          <feComposite
            in2="hardAlpha"
            k2="-1"
            k3="1"
            operator="arithmetic"
          ></feComposite>
          <feColorMatrix
            values="0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 0.25 0"
          ></feColorMatrix>
          <feBlend
            in2="shape"
            result="effect1_innerShadow_66_6341"
          ></feBlend>
        </filter>
      </defs>
    </svg>
  </button>
</div>
`;

    const div = document.createElement("div");
    div.innerHTML = buttonHTML.trim();
    document.body.appendChild(div.firstChild);
  }

  insertWonderChatButton();
</script>