// ── Burger menu mobile ──
const burger = document.getElementById('burgerBtn');
const nav    = document.getElementById('mainNav');

if (burger && nav) {
    burger.addEventListener('click', () => {
        nav.classList.toggle('open');
    });
}

// ── Smooth scroll sur les ancres ──
document.querySelectorAll('a[href*="#"]').forEach(link => {
    link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        const hashIndex = href.indexOf('#');
        const hash = href.substring(hashIndex);
        const target = document.querySelector(hash);

        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ═══════════════════════════════════════════
//   RESERVATION — Stepper 3 étapes
// ═══════════════════════════════════════════

let currentStep = 1;

// ── Navigation entre étapes ──
function goToStep(n) {
    // Cache étape courante
    document.getElementById(`step-${currentStep}`).classList.remove('active');
    document.getElementById(`step-indicator-${currentStep}`).classList.remove('active');
    document.getElementById(`step-indicator-${currentStep}`).classList.add('done');

    // Met à jour les lignes
    document.querySelectorAll('.step-line').forEach((line, i) => {
        if (i < n - 1) line.classList.add('done');
        else line.classList.remove('done');
    });

    currentStep = n;

    // Affiche nouvelle étape
    document.getElementById(`step-${currentStep}`).classList.add('active');
    document.getElementById(`step-indicator-${currentStep}`).classList.remove('done');
    document.getElementById(`step-indicator-${currentStep}`).classList.add('active');

    // Scroll haut du formulaire
    document.getElementById('reservationForm').scrollIntoView({ behavior: 'smooth' });
}

// ── Étape 1 → 2 ──
const btnStep1Next = document.getElementById('btn-step1-next');
if (btnStep1Next) {
    btnStep1Next.addEventListener('click', () => {
        const selected = document.querySelector('input[name="service_id"]:checked');
        if (!selected) {
            alert('Veuillez sélectionner un service.');
            return;
        }
        goToStep(2);
    });
}

// ── Étape 2 → 1 ──
const btnStep2Prev = document.getElementById('btn-step2-prev');
if (btnStep2Prev) {
    btnStep2Prev.addEventListener('click', () => {
        document.getElementById(`step-indicator-1`).classList.remove('done');
        goToStep(1);
    });
}

// ── Étape 2 → 3 ──
const btnStep2Next = document.getElementById('btn-step2-next');
if (btnStep2Next) {
    btnStep2Next.addEventListener('click', () => {
        const date  = document.getElementById('date_rdv').value;
        const heure = document.getElementById('heure_rdv_hidden').value;
        if (!date)  { alert('Veuillez choisir une date.'); return; }
        if (!heure) { alert('Veuillez choisir un créneau horaire.'); return; }

        // Mise à jour récap
        const serviceNom = document.querySelector('input[name="service_id"]:checked')?.dataset.nom;
        document.getElementById('recap-service').textContent = serviceNom || '—';
        document.getElementById('recap-date').textContent    = new Date(date).toLocaleDateString('fr-FR', {weekday:'long', day:'numeric', month:'long'});
        document.getElementById('recap-heure').textContent   = heure;

        goToStep(3);
    });
}

// ── Étape 3 → 2 ──
const btnStep3Prev = document.getElementById('btn-step3-prev');
if (btnStep3Prev) {
    btnStep3Prev.addEventListener('click', () => {
        document.getElementById(`step-indicator-2`).classList.remove('done');
        goToStep(2);
    });
}

// ── Sélection service (highlight) ──
document.querySelectorAll('.service-option input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.service-option').forEach(el => el.classList.remove('selected'));
        this.closest('.service-option').classList.add('selected');
    });
});

// ── Chargement créneaux selon date ──
const dateInput = document.getElementById('date_rdv');
if (dateInput) {
    dateInput.addEventListener('change', function() {
        const date      = this.value;
        const serviceId = document.querySelector('input[name="service_id"]:checked')?.value;
        const container = document.getElementById('creneaux-container');

        if (!date) return;

        container.innerHTML = '<p style="color:var(--muted-foreground);">Chargement...</p>';

        fetch(`ajax/get_creneaux.php?date=${date}&service_id=${serviceId || ''}`)
            .then(r => r.json())
            .then(data => {
                if (!data.creneaux || data.creneaux.length === 0) {
                    container.innerHTML = '<p style="color:var(--muted-foreground);">Aucun créneau disponible ce jour.</p>';
                    return;
                }
                container.innerHTML = '';
                data.creneaux.forEach(h => {
                    const btn = document.createElement('button');
                    btn.type      = 'button';
                    btn.className = 'creneau-btn' + (h.disponible ? '' : ' indisponible');
                    btn.textContent = h.heure;
                    btn.disabled  = !h.disponible;
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.creneau-btn').forEach(b => b.classList.remove('selected'));
                        this.classList.add('selected');
                        document.getElementById('heure_rdv_hidden').value = h.heure;
                    });
                    container.appendChild(btn);
                });
            })
            .catch(() => {
                container.innerHTML = '<p style="color:#f06080;">Erreur de chargement.</p>';
            });
    });
}