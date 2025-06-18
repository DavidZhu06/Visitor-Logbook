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
ctx.lineWidth = 2;
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
document.getElementById('submitBtn').addEventListener('click', () => {
  if (signatureData.length > 0) {
    const signature = canvas.toDataURL('image/png');
    document.getElementById('signature').value = signature;
  } else {
    alert('Please provide a signature before submitting.');
  }
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
      companyName: "Imperial Distributors Canada Inc.",
      policyTitle: "Visitor Access Policy",
      readInstruction: "PLEASE READ IN ITS ENTIRETY BEFORE SIGNING.",
      nonDisclosureTitle: "I. NON-DISCLOSURE POLICY:",
      nonDisclosureText: `I acknowledge that the information received or otherwise obtained, directly or indirectly, while at Imperial Distributors Canada Inc. (Company) is confidential and that the nature of the business is such that the following conditions are reasonable. Therefore: <br>I agree that I will not, at any time, either during the term of this agreement or thereafter, divulge to any person, firm or corporation any information received with regard to the personal/ private information or other affairs of the Company. All such information shall be kept confidential and shall not in any manner be revealed to anyone, except as expressly authorized for the performance of the operational duties or otherwise required by law. Any information causing irreparable harm to the Company, will be subject to immediate injunctive action by the Company, including any remedies available in law.<br> I recognize that I may be given access to confidential information belonging to IDCI through my relationship with the Company or as a result of my access to Company’s premises. The Company’s private operations and trade secrets consist of information and materials that are valuable and not generally known by Company’s competitors. <br>In consideration of being admitted to Company’s facilities, I will hold the strictest confidence any trade secrets or confidential information, whether oral or written, that is disclosed to me. I will not remove any books, records or documents or copies thereof, in any format (i.e. electronic or otherwise) from the premises without Company’s written permission. <strong>I will not photograph or otherwise record any information which I may have access to during my visit.</strong><br>IDCI shall be entitled to and has the right to obtain an injunction to ensure compliance with this agreement. A failure to comply with these restrictive agreements constitutes a violation of business conduct and maybe cause for legal action, termination of further business affiliation, and access to Company’s premises.`,
      securityTitle: "II. SECURITY, SAFETY & GMP POLICY:",
      securityIntro: "I acknowledge that I will:",
      escortRule: "Be escorted by an IDCI employee while inside the building.",
      accessCardRule: "Visibly display the visitor access card (if assigned) at all times while inside the building and return it upon my exit out of the building.",
      signInRule: "Sign-in and sign-out of restricted areas where required.",
      doorsRule: "Maintain all exterior and interior doors in a closed position.",
      evacuationRule: "Immediately evacuate to the nearest exit upon emergency alarm. Once evacuated, the building is not to be re-entered until the incident is officially declared as over.",
      foodRule: "Not bring any food or beverage or smoke inside the warehouse.",
      productRule: "Not handle any product if permitted inside the warehouse unless with prior permission.",
      reviewInstruction: "Please review these policies carefully before signing below.",
      signInstruction: "Please e-sign below:",
      clearBtn: "Clear",
      submitBtn: "Next",
      languageToggle: "FR"
    },
    fr: {
      title: "Politique d'accès des visiteurs",
      companyName: "Imperial Distributors Canada Inc.",
      policyTitle: "Politique d'accès des visiteurs",
      readInstruction: "VEUILLEZ LIRE ENTIÈREMENT AVANT DE SIGNER.",
      nonDisclosureTitle: "I. POLITIQUE DE NON-DIVULGATION :",
      nonDisclosureText: `Je reconnais que les informations reçues ou obtenues, directement ou indirectement, lors de ma présence chez Imperial Distributors Canada Inc. (la Société) sont confidentielles et que la nature de l'entreprise est telle que les conditions suivantes sont raisonnables. Par conséquent : <br>Je m'engage à ne jamais, pendant la durée de cet accord ou après, divulguer à toute personne, entreprise ou société des informations reçues concernant les informations personnelles/privées ou autres affaires de la Société. Toutes ces informations doivent rester confidentielles et ne doivent en aucun cas être révélées à quiconque, sauf autorisation expresse pour l'exécution des tâches opérationnelles ou si requis par la loi. Toute information causant un préjudice irréparable à la Société fera l'objet d'une action en injonction immédiate par la Société, y compris tous les recours disponibles en droit.<br> Je reconnais que je pourrais avoir accès à des informations confidentielles appartenant à IDCI en raison de ma relation avec la Société ou de mon accès aux locaux de la Société. Les opérations privées et les secrets commerciaux de la Société consistent en des informations et des matériaux précieux qui ne sont généralement pas connus des concurrents de la Société. <br>En contrepartie de mon admission dans les installations de la Société, je garderai la plus stricte confidentialité concernant tout secret commercial ou information confidentielle, qu'elle soit orale ou écrite, qui m'est divulguée. Je ne retirerai aucun livre, registre ou document, ni leurs copies, sous quelque format que ce soit (par exemple, électronique ou autre) des locaux sans l'autorisation écrite de la Société. <strong>Je ne photographierai ni n'enregistrerai de quelque manière que ce soit les informations auxquelles j'aurai accès pendant ma visite.</strong><br>IDCI aura le droit d'obtenir une injonction pour assurer le respect de cet accord. Le non-respect de ces accords restrictifs constitue une violation de la conduite des affaires et peut entraîner des poursuites judiciaires, la fin de toute affiliation commerciale future et l'accès aux locaux de la Société.`,
      securityTitle: "II. POLITIQUE DE SÉCURITÉ, DE SANTÉ ET DE BPF :",
      securityIntro: "Je reconnais que je vais :",
      escortRule: "Être escorté par un employé d'IDCI à l'intérieur du bâtiment.",
      accessCardRule: "Afficher visiblement la carte d'accès visiteur (si attribuée) en tout temps à l'intérieur du bâtiment et la retourner à ma sortie du bâtiment.",
      signInRule: "Me connecter et me déconnecter des zones restreintes lorsque requis.",
      doorsRule: "Maintenir toutes les portes extérieures et intérieures en position fermée.",
      evacuationRule: "Évacuer immédiatement vers la sortie la plus proche en cas d'alarme d'urgence. Une fois évacué, le bâtiment ne doit pas être réintégré tant que l'incident n'est pas officiellement déclaré terminé.",
      foodRule: "Ne pas apporter de nourriture, de boisson ou fumer à l'intérieur de l'entrepôt.",
      productRule: "Ne pas manipuler de produit si je suis autorisé à entrer dans l'entrepôt, sauf avec une autorisation préalable.",
      reviewInstruction: "Veuillez examiner attentivement ces politiques avant de signer ci-dessous.",
      signInstruction: "Veuillez signer électroniquement ci-dessous :",
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
