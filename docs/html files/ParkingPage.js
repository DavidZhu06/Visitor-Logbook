// Ensure no global conflicts by wrapping in an IIFE (Immediately Invoked Function Expression)
(function () {
  // Language data object
  const translations = {
    en: {
      languageToggle: "FR",
      heading: "Parking Guidelines",
      //guidelinesHeading: "ATTENTION VISITORS - PARKING NOTICE",
      paragraph1: "All visitors are required to display a valid <b>Visitor Parking Pass</b> while parked on company property. Please be sure to:",
      paragraph2: "<b>1. Designated Areas:</b> Park only in the areas marked for Imperial Distributors. <b>Unauthorized vehicles will be towed at the owner’s expense.</b>",
      paragraph3: "<b>2. Parking Pass:</b> Request a Visitor Parking Pass and clearly display it on your vehicle’s dashboard with the printed side visible from outside the car.",
      paragraph4: "If you have any questions or are unsure where to park, our team at the front desk will be happy to assist you. Thank you for your cooperation.",
      nextButton: "Next"
    },
    fr: {
      languageToggle: "EN",
      heading: "Informations de stationnement",
      //guidelinesHeading: "ATTENTION VISITEURS - AVIS DE STATIONNEMENT",
      paragraph1: "Tous les visiteurs doivent afficher un <b>Permis de stationnement pour visiteurs</b> valide lorsqu’ils sont garés sur la propriété de l’entreprise. Veuillez vous assurer de:",
      paragraph2: "<b>1. Zones désignées: </b> Parc uniquement dans les zones marquées pour les distributeurs impériaux. <b>Les véhicules non autorisés seront remorqués à la charge du propriétaire.</b>",
      paragraph3: "<b>2. Carte de stationnement: </b> Demandez une carte de stationnement pour visiteurs et affichez-la clairement sur le tableau de bord de votre véhicule avec le côté imprimé visible de l’extérieur de la voiture.",
      paragraph4: "Si vous avez des questions ou si vous ne savez pas où vous garer, notre équipe à la réception se fera un plaisir de vous aider. Merci pour votre coopération.",
      nextButton: "Suivant"
    }
  };

  // Set initial language
  let currentLanguage = 'en';

  // Function to update all text based on language
  function updateLanguage(lang) {
    currentLanguage = lang;
    const content = translations[lang];

    // Update by data-key attribute
    document.querySelectorAll('[data-key]').forEach(el => {
      const key = el.getAttribute('data-key');
      if (content[key]) el.innerHTML = content[key];
    });

    document.documentElement.lang = lang;
    localStorage.setItem('preferredLanguage', lang);
  }

  // Load saved preference or default
  const savedLang = localStorage.getItem('preferredLanguage') || 'en';
  updateLanguage(savedLang);

  // Toggle on button click
  const toggleButton = document.getElementById('languageToggle');
  if (toggleButton) {
    toggleButton.addEventListener('click', () => {
      const newLang = currentLanguage === 'en' ? 'fr' : 'en';
      updateLanguage(newLang);
    });
  }

})();
