const canvas = document.getElementById('signatureCanvas'); /*.getElementbyID is a built-in js method that searches document (whole loaded HTML page) for element with ID attribute equal to signatureCanvas
const canvas: stores that HTML element in constant variable called canvas*/
const ctx = canvas.getContext('2d'); /*Saves 2d drawing context of <canvas> element to ctx variable */

//below variables used to track whether user is drawing and store last touch coordinates 
let isDrawing = false; //means user is not drawing at the start, set to true when user clicks mouse or touches canvas
let lastX = 0;
let lastY = 0;
let signatureData = []; // Array to store signature data if needed
let currentLanguage = 'en'; // Default language

//specify drawing style
ctx.lineWidth = 3;
ctx.lineCap = 'round';
ctx.lineJoin = 'round'; // Smooths line connections
ctx.strokeStyle = '#000';

// Adjust canvas resolution to match CSS size
function resizeCanvas() {
  const rect = canvas.getBoundingClientRect();
  canvas.width = rect.width * window.devicePixelRatio;
  canvas.height = rect.height * window.devicePixelRatio;
  ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
  redrawSignature(); // Redraw signature after resize
}

// Redraw signature from stored points
function redrawSignature() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  ctx.beginPath();
  signatureData.forEach(segment => {
    ctx.moveTo(segment[0].x, segment[0].y);
    for (let i = 1; i < segment.length; i++) {
      ctx.lineTo(segment[i].x, segment[i].y);
    }
    ctx.stroke();
  });
  ctx.beginPath();
}

// Get canvas coordinates
function getCanvasCoordinates(event) {
  const rect = canvas.getBoundingClientRect();
  if (event.type.startsWith('touch')) {
    return {
      x: event.touches[0].clientX - rect.left,
      y: event.touches[0].clientY - rect.top
    };
  }
  return {
    x: event.clientX - rect.left,
    y: event.clientY - rect.top
  };
}

// Start drawing
function startDrawing(e) {
  e.preventDefault();
  isDrawing = true;
  const coords = getCanvasCoordinates(e);
  lastX = coords.x;
  lastY = coords.y;
  signatureData.push([{ x: lastX, y: lastY }]); // Start new segment
  ctx.beginPath();
}

// Draw on canvas
function draw(e) {
  if (!isDrawing) return;
  e.preventDefault();
  const coords = getCanvasCoordinates(e);
  ctx.moveTo(lastX, lastY);
  ctx.lineTo(coords.x, coords.y);
  ctx.stroke();
  signatureData[signatureData.length - 1].push({ x: coords.x, y: coords.y }); // Add to current segment
  lastX = coords.x;
  lastY = coords.y;
}

// Stop drawing
function stopDrawing() {
  isDrawing = false;
  ctx.beginPath();
}

// Event listeners
canvas.addEventListener('mousedown', startDrawing);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', stopDrawing);
canvas.addEventListener('mouseout', stopDrawing);
canvas.addEventListener('touchstart', startDrawing);
canvas.addEventListener('touchmove', draw);
canvas.addEventListener('touchend', stopDrawing);
canvas.addEventListener('touchcancel', stopDrawing);

// Clear canvas
document.getElementById('clearBtn').addEventListener('click', () => {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  signatureData = []; // Clear stored points
  document.getElementById('signature').value = '';
});

// Update signature input before form submission
const form = document.getElementById('signatureForm');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit', (e) => {
  // If no signature, stop submission
  if (signatureData.length === 0) {
    e.preventDefault();
    alert('Please provide a signature before submitting.');
    return;
  }

  // Save signature to hidden input
  const signature = canvas.toDataURL('image/png');
  document.getElementById('signature').value = signature;

  // Disable button + change text
  submitBtn.disabled = true;
  submitBtn.innerText = "Next...";
  submitBtn.style.backgroundColor = "#999"; // greyed out

  // Optional fallback: re-enable after 5s if needed
  setTimeout(() => {
    submitBtn.disabled = false;
    submitBtn.innerText = "Next";
    submitBtn.style.backgroundColor = ""; // reset style
  }, 5000);
});

// Resize canvas on load and window resize
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// Ensure no global conflicts by wrapping in an IIFE (Immediately Invoked Function Expression)
(function() {
  // Language data object
  const translations = {
    en: {
      title: "Visitor Access Policy",
      pageTitle: "Visitor Access Policy",
      // policyTitle: "Visitor Access Policy",
      readInstruction: "PLEASE READ IN ITS ENTIRETY BEFORE SIGNING.",
      nonDisclosureTitle: "I. NON-DISCLOSURE POLICY:",
      nonDisclosureText: `I understand that during my time at Imperial Distributors Canada Inc. (IDCI), I may access confidential or proprietary information. I agree not to disclose any such information, including personal, private, operational, or trade secrets, to anyone during or after my relationship with the Company, unless authorized or legally required. <br><br>
      I will not remove, copy, photograph, or otherwise record any documents or information, in any form, without written permission from IDCI. <b>I will not photograph or otherwise record any information which I may have access to during my visit. </b> <br><br>
      I acknowledge that this information is valuable and not publicly known, and that improper disclosure could cause serious harm. IDCI may seek legal or injunctive action for any breach. Violations may result in legal consequences and termination of access to the Company or its premises.`,
      securityTitle: "II. SECURITY, SAFETY & GMP POLICY:",
      securityIntro: "I acknowledge that I will:",
      escortRule: "Be escorted by an IDCI team member while inside the building.",
      accessCardRule: "Visibly display the visitor access card (if assigned) at all times while inside the building and return it upon my exit out of the building.",
      signInRule: "Sign-in and sign-out of restricted areas where required.",
      doorsRule: "Maintain all exterior and interior doors in a closed position.",
      evacuationRule: "Immediately evacuate to the nearest exit upon emergency alarm. Once evacuated, the building is not to be re-entered until the incident is officially declared as over.",
      foodRule: "Not bring any food or beverage or smoke inside the warehouse.",
      productRule: "Not handle any product if permitted inside the warehouse unless with prior permission.",
      //reviewInstruction: "Please review these policies carefully before signing below.",
      signInstruction: "Please review these policies carefully and e-sign below:",
      clearBtn: "Clear",
      submitBtn: "Next",
      languageToggle: "FR"
    },
    fr: {
      title: "Politique d'accès des visiteurs",
      pageTitle: "Politique d'accès des visiteurs",
      //policyTitle: "Politique d'accès des visiteurs",
      readInstruction: "VEUILLEZ LIRE ENTIÈREMENT AVANT DE SIGNER.",
      nonDisclosureTitle: "I. POLITIQUE DE NON-DIVULGATION :",
      nonDisclosureText: `Je comprends que pendant mon temps chez Imperial Distributors Canada Inc. (IDCI), je peux accéder à des informations confidentielles ou exclusives. Je suis d’accord pour ne pas divulguer de telles informations, y compris des secrets personnels, privés, opérationnels ou commerciaux, à quiconque pendant ou après ma relation avec la Société, sauf autorisation ou exigence légale. <br><br>
      Je ne retirerai, ne copierai, ne photographierai ni n’enregistrerai aucun document ou information, sous quelque forme que ce soit, sans l’autorisation écrite d’IDCI. <b>Je ne photographierai ni n’enregistrerai aucune information à laquelle j’aurai accès lors de ma visite. </b> <br><br>
      Je reconnais que cette information est précieuse et non connue du public, et qu’une divulgation inappropriée pourrait causer un préjudice grave. IDCI peut demander une action légale ou par injonction pour toute violation. Les violations peuvent entraîner des conséquences juridiques et la résiliation de l’accès à la Société ou à ses locaux.`,
      securityTitle: "II. POLITIQUE DE SÉCURITÉ, DE SANTÉ ET DE BPF :",
      securityIntro: "Je reconnais que je vais :",
      escortRule: "Être escorté par un employé d'IDCI à l'intérieur du bâtiment.",
      accessCardRule: "Afficher visiblement la carte d'accès visiteur (si attribuée) en tout temps à l'intérieur du bâtiment et la retourner à ma sortie du bâtiment.",
      signInRule: "Me connecter et me déconnecter des zones restreintes lorsque requis.",
      doorsRule: "Maintenir toutes les portes extérieures et intérieures en position fermée.",
      evacuationRule: "Évacuer immédiatement vers la sortie la plus proche en cas d'alarme d'urgence. Une fois évacué, le bâtiment ne doit pas être réintégré tant que l'incident n'est pas officiellement déclaré terminé.",
      foodRule: "Ne pas apporter de nourriture, de boisson ou fumer à l'intérieur de l'entrepôt.",
      productRule: "Ne pas manipuler de produit si je suis autorisé à entrer dans l'entrepôt, sauf avec une autorisation préalable.",
      //reviewInstruction: "Veuillez examiner attentivement ces politiques avant de signer ci-dessous.",
      signInstruction: "Veuillez examiner attentivement ces politiques et signer électroniquement ci-dessous:",
      clearBtn: "Effacer",
      submitBtn: "Suivant",
      languageToggle: "EN"
    }
  };

  // Declare currentLanguage within the IIFE scope
  let currentLanguage = 'en';

  // Function to update page content based on language
  function updateLanguage(lang) {
    currentLanguage = lang;
    document.querySelectorAll('[data-key]').forEach(element => {
      const key = element.getAttribute('data-key');
      element.innerHTML = translations[lang][key];
    });
    document.documentElement.lang = lang; // Update HTML lang attribute
    localStorage.setItem('preferredLanguage', lang); // Save preference
  }

  // Load saved language preference or default to English
  const savedLang = localStorage.getItem('preferredLanguage') || 'en';
  updateLanguage(savedLang);

  // Toggle language on button click
  document.getElementById('languageToggle').addEventListener('click', () => {
    const newLang = currentLanguage === 'en' ? 'fr' : 'en';
    updateLanguage(newLang);
  });

})();
