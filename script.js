document.addEventListener("DOMContentLoaded", () => {
  // ─── BURGER MENU ──────────────────────────────────
  const burger = document.getElementById("burger");
  const navLinksEl = document.getElementById("nav-links");

  if (burger) {
    burger.addEventListener("click", () => {
      burger.classList.toggle("open");
      navLinksEl.classList.toggle("open");
    });
  }

  // ─── NAVBAR SCROLL ────────────────────────────────
  const navbar = document.getElementById("navbar");
  window.addEventListener("scroll", () => {
    navbar.classList.toggle("scrolled", window.scrollY > 30);
  });

  // ─── REVEAL ON SCROLL ─────────────────────────────
  function triggerReveal() {
    const reveals = document.querySelectorAll(".reveal");
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry, i) => {
          if (entry.isIntersecting) {
            setTimeout(() => {
              entry.target.classList.add("visible");
            }, i * 80);
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 },
    );

    reveals.forEach((el) => observer.observe(el));
  }

  triggerReveal();

  // ─── SLIDER (accueil uniquement) ──────────────────
  const slides = document.querySelectorAll(".slide");
  if (slides.length > 0) {
    const dotsContainer = document.getElementById("sliderDots");
    let currentSlide = 0;
    let sliderInterval;

    slides.forEach((_, i) => {
      const dot = document.createElement("div");
      dot.classList.add("dot");
      if (i === 0) dot.classList.add("active");
      dot.addEventListener("click", () => goToSlide(i));
      dotsContainer.appendChild(dot);
    });

    function goToSlide(n) {
      slides[currentSlide].classList.remove("active");
      dotsContainer
        .querySelectorAll(".dot")
        [currentSlide].classList.remove("active");
      currentSlide = (n + slides.length) % slides.length;
      slides[currentSlide].classList.add("active");
      dotsContainer
        .querySelectorAll(".dot")
        [currentSlide].classList.add("active");
    }

    function nextSlide() {
      goToSlide(currentSlide + 1);
    }
    function prevSlide() {
      goToSlide(currentSlide - 1);
    }

    document.getElementById("sliderNext").addEventListener("click", () => {
      nextSlide();
      resetInterval();
    });
    document.getElementById("sliderPrev").addEventListener("click", () => {
      prevSlide();
      resetInterval();
    });

    function resetInterval() {
      clearInterval(sliderInterval);
      sliderInterval = setInterval(nextSlide, 5000);
    }

    sliderInterval = setInterval(nextSlide, 5000);
  }

  // ─── CAROUSEL (accueil uniquement) ────────────────
  const carousel = document.getElementById("carousel");
  if (carousel) {
    const items = carousel.querySelectorAll(".carousel-item");
    let carouselOffset = 0;

    function getVisibleCount() {
      if (window.innerWidth <= 480) return 1;
      if (window.innerWidth <= 768) return 2;
      return 3;
    }

    function getItemWidth() {
      const visible = getVisibleCount();
      const gap = 24;
      return (
        (carousel.parentElement.offsetWidth - gap * (visible - 1)) / visible
      );
    }

    function updateCarousel() {
      const visible = getVisibleCount();
      const maxOffset = items.length - visible;
      carouselOffset = Math.min(carouselOffset, maxOffset);
      const itemW = getItemWidth();
      const gap = 24;
      carousel.style.transform = `translateX(-${carouselOffset * (itemW + gap)}px)`;
    }

    document.getElementById("carouselNext").addEventListener("click", () => {
      const visible = getVisibleCount();
      const max = items.length - visible;
      carouselOffset = carouselOffset < max ? carouselOffset + 1 : 0;
      updateCarousel();
    });

    document.getElementById("carouselPrev").addEventListener("click", () => {
      const visible = getVisibleCount();
      const max = items.length - visible;
      carouselOffset = carouselOffset > 0 ? carouselOffset - 1 : max;
      updateCarousel();
    });

    window.addEventListener("resize", updateCarousel);
  }

  // ─── MODALES PRESTATIONS (prestations uniquement) ─
  const modalData = {
    conception: {
      title: "Conception & réalisation — Réalisations",
      images: [
        "https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&q=80",
        "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80",
        "https://images.unsplash.com/photo-1599598425947-5202edd56bdb?w=600&q=80",
        "https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=600&q=80",
      ],
    },
    entretien: {
      title: "Entretien des espaces verts — Réalisations",
      images: [
        "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80",
        "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80",
        "https://images.unsplash.com/photo-1501004318641-b39e6451bec6?w=600&q=80",
        "https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&q=80",
      ],
    },
    haies: {
      title: "Taille des haies — Réalisations",
      images: [
        "https://images.unsplash.com/photo-1599598425947-5202edd56bdb?w=600&q=80",
        "https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=600&q=80",
        "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80",
        "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80",
      ],
    },
    elagage: {
      title: "Élagage & abattage — Réalisations",
      images: [
        "https://images.unsplash.com/photo-1501004318641-b39e6451bec6?w=600&q=80",
        "https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&q=80",
        "https://images.unsplash.com/photo-1599598425947-5202edd56bdb?w=600&q=80",
        "https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=600&q=80",
      ],
    },
    compost: {
      title: "Valorisation déchets verts — Réalisations",
      images: [
        "https://images.unsplash.com/photo-1464226184884-fa280b87c399?w=600&q=80",
        "https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80",
        "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&q=80",
        "https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&q=80",
      ],
    },
  };

  const modalOverlay = document.getElementById("modalOverlay");
  if (modalOverlay) {
    const modalTitle = document.getElementById("modalTitle");
    const modalGallery = document.getElementById("modalGallery");
    const modalClose = document.getElementById("modalClose");

    document.querySelectorAll(".presta-card[data-modal]").forEach((card) => {
      card.addEventListener("click", () => {
        const key = card.getAttribute("data-modal");
        const data = modalData[key];
        if (!data) return;
        modalTitle.textContent = data.title;
        modalGallery.innerHTML = data.images
          .map(
            (src) =>
              `<img src="${src}" alt="Réalisation Canopées" loading="lazy" />`,
          )
          .join("");
        modalOverlay.classList.add("open");
        document.body.style.overflow = "hidden";
      });
    });

    modalClose.addEventListener("click", closeModal);
    modalOverlay.addEventListener("click", (e) => {
      if (e.target === modalOverlay) closeModal();
    });

    function closeModal() {
      modalOverlay.classList.remove("open");
      document.body.style.overflow = "";
    }
  }

  // ─── LÉGAL MODALES ────────────────────────────────
  const legalOverlay = document.getElementById("legalOverlay");
  if (legalOverlay) {
    const legalTitle = document.getElementById("legalTitle");
    const legalContent = document.getElementById("legalContent");
    const legalClose = document.getElementById("legalClose");

    const legalTexts = {
      mentions: {
        title: "Mentions légales",
        content: `
          <h4>Éditeur du site</h4>
          <p>Canopées SAS — 12 Allée des Jardins, 31000 Toulouse<br>
          Capital social : 10 000 €<br>
          SIRET : 123 456 789 00010<br>
          Directeurs de publication : Bob Dupont & Tom Martin</p>
          <h4>Hébergement</h4>
          <p>Ce site est hébergé par OVH SAS, 2 rue Kellermann, 59100 Roubaix.</p>
          <h4>Propriété intellectuelle</h4>
          <p>Tout le contenu de ce site (textes, images, graphismes) est la propriété exclusive de Canopées et est protégé par le droit d'auteur.</p>
          <h4>Responsabilité</h4>
          <p>Canopées s'efforce de fournir des informations exactes mais ne peut garantir l'exactitude ou l'exhaustivité des informations diffusées.</p>
        `,
      },
      cgu: {
        title: "Conditions Générales d'Utilisation",
        content: `
          <h4>Article 1 — Objet</h4>
          <p>Les présentes CGU définissent les conditions d'accès et d'utilisation du site canopees.fr.</p>
          <h4>Article 2 — Accès au site</h4>
          <p>L'accès au site est libre et gratuit. Canopées se réserve le droit de suspendre l'accès à tout ou partie du site à tout moment.</p>
          <h4>Article 3 — Données personnelles</h4>
          <p>Les données collectées via le formulaire de contact sont utilisées uniquement pour répondre à vos demandes. Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression de vos données.</p>
          <h4>Article 4 — Cookies</h4>
          <p>Ce site peut utiliser des cookies techniques nécessaires à son fonctionnement. Aucun cookie publicitaire n'est utilisé.</p>
        `,
      },
      cgv: {
        title: "Conditions Générales de Vente",
        content: `
          <h4>Article 1 — Champ d'application</h4>
          <p>Les présentes CGV s'appliquent à toutes les prestations réalisées par Canopées auprès de ses clients particuliers, professionnels et collectivités.</p>
          <h4>Article 2 — Devis et commandes</h4>
          <p>Tout projet fait l'objet d'un devis gratuit signé par le client avant démarrage. Le devis signé vaut bon de commande.</p>
          <h4>Article 3 — Tarifs</h4>
          <p>Les prix indiqués sont en euros HT. La TVA applicable est de 10% pour les travaux d'espaces verts. Les tarifs peuvent être révisés chaque année au 1er janvier.</p>
          <h4>Article 4 — Paiement</h4>
          <p>Un acompte de 30% est demandé à la signature du devis. Le solde est payable à réception de facture sous 30 jours.</p>
          <h4>Article 5 — Garanties</h4>
          <p>Canopées garantit la reprise des végétaux déficients dans les 12 mois suivant la plantation, sauf cas de force majeure ou mauvais entretien du client.</p>
        `,
      },
    };

    document.querySelectorAll("[data-legal]").forEach((el) => {
      el.addEventListener("click", (e) => {
        e.preventDefault();
        const key = el.getAttribute("data-legal");
        const data = legalTexts[key];
        if (!data) return;
        legalTitle.textContent = data.title;
        legalContent.innerHTML = data.content;
        legalOverlay.classList.add("open");
        document.body.style.overflow = "hidden";
      });
    });

    legalClose.addEventListener("click", closeLegal);
    legalOverlay.addEventListener("click", (e) => {
      if (e.target === legalOverlay) closeLegal();
    });

    function closeLegal() {
      legalOverlay.classList.remove("open");
      document.body.style.overflow = "";
    }
  }

  // ─── FERMER MODALES AVEC ESCAPE ───────────────────
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const mo = document.getElementById("modalOverlay");
      const lo = document.getElementById("legalOverlay");
      if (mo) mo.classList.remove("open");
      if (lo) lo.classList.remove("open");
      document.body.style.overflow = "";
    }
  });
});
