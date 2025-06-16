// Ensure no global conflicts by wrapping in an IIFE (Immediately Invoked Function Expression)
(function () {
  // Language data object
  const translations = {
    en: {
      languageToggle: "FR",
      heading: "Parking Information",
      guidelinesHeading: "Parking Guidelines",
      paragraph1: "Welcome to the parking information page for Imperial Distributors Canada Inc. This measure is to prevent unauthorized vehicles from occupying spaces reserved for IDCI and Pure employees/visitors. Below are <b>general guidelines for parking</b> at our facility:",
      paragraph2: "<b>1. Designated Areas:</b> Please park only in the areas marked for IDCI/PURE parking. <b>Unauthorized vehicles will be towed at the owner’s expense.</b>",
      paragraph3: "<b>2. Parking Pass:</b> Parking passes must be clearly displayed or hung on your rearview mirror at all times while on company property. <b>Please make sure to get a parking pass upon arrival.</b>",
      paragraph4: "<b>3. Time Limits:</b> Parking is limited to the duration of your visit. Overnight parking is not permitted without prior approval.",
      nextButton: "Next"
    },
    fr: {
      languageToggle: "EN",
      heading: "Informations sur le stationnement",
      guidelinesHeading: "Règles de stationnement",
      paragraph1: "Bienvenue sur la page d'informations sur le stationnement d'Imperial Distributors Canada Inc. Cette mesure vise à empêcher les véhicules non autorisés d’occuper les places réservées aux employés/visiteurs de IDCI et Pure. Voici les règles <b>générales de stationnement</b> dans notre établissement:",
      paragraph2: "<b>1. Zones désignées:</b> Veuillez stationner uniquement dans les zones marquées pour le stationnement IDCI/PURE. <b>Les véhicules non autorisés seront remorqués aux frais du propriétaire.</b>",
      paragraph3: "<b>2. Pass de stationnement:</b> Les passes de stationnement doivent être clairement affichées ou accrochées à votre rétroviseur pendant toute la durée de votre présence sur la propriété de l’entreprise. <b>Veuillez vous assurer d’en obtenir un à votre arrivée.</b>",
      paragraph4: "<b>3. Durée limite:</b> Le stationnement est limité à la durée de votre visite. Le stationnement de nuit n’est pas autorisé sans approbation préalable.",
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
