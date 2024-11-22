document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = parseInt(urlParams.get('id'), 10);

    if (id) {
        // Carica i dati per il testo e la domanda
        loadData('data_testi.json').then(data => {
            const item = data.find(item => item.id === id);
            if (item) {
                document.getElementById('content').innerHTML = `
                    <h2>${item.title}</h2>
                    <p>${item.description}</p>
                `;
            } else {
                document.getElementById('content').innerHTML = '<p>Contenuto non trovato.</p>';
            }
        });

        // Carica la domanda e le opzioni
        const questions = [
            { question: "Qual è la capitale d'Italia?", options: ["Roma", "Milano", "Napoli", "Torino"], correctAnswer: "Roma" },
            { question: "In che anno è stata fondata Roma?", options: ["753 a.C.", "476 d.C.", "1000", "1492"], correctAnswer: "753 a.C." },
            { question: "Chi ha scritto la Divina Commedia?", options: ["Dante Alighieri", "Petrarca", "Boccaccio", "Manzoni"], correctAnswer: "Dante Alighieri" }
        ];

        let currentQuestionIndex = 0;

        function loadQuestion(index) {
            const question = questions[index];
            document.getElementById('question').textContent = question.question;
            const optionsContainer = document.getElementById('options');
            optionsContainer.innerHTML = '';
            question.options.forEach(option => {
                const li = document.createElement('li');
                li.innerHTML = `<label><input type="radio" name="question${index}" value="${option}"> ${option}</label>`;
                optionsContainer.appendChild(li);
            });
            document.getElementById('next-question').style.display = 'none'; // Nascondi il pulsante inizialmente
            document.getElementById('prev-question').style.display = index > 0 ? 'inline-block' : 'none'; // Mostra il pulsante indietro se non siamo sulla prima domanda
        }

        // Aggiungi un evento per il pulsante della freccia (prossima domanda)
        document.getElementById('next-question').addEventListener('click', function() {
            currentQuestionIndex++;
            if (currentQuestionIndex < questions.length) {
                loadQuestion(currentQuestionIndex);
            } else {
                document.getElementById('question').textContent = 'Hai completato tutte le domande!';
                document.getElementById('options').innerHTML = '';
                document.getElementById('next-question').style.display = 'none';
                document.getElementById('prev-question').style.display = 'none';
            }
        });

        // Aggiungi un evento per il pulsante "torna indietro"
        document.getElementById('prev-question').addEventListener('click', function() {
            currentQuestionIndex--;
            if (currentQuestionIndex >= 0) {
                loadQuestion(currentQuestionIndex);
            }
        });

        // Aggiungi un evento per la selezione delle opzioni
        document.getElementById('options').addEventListener('change', function() {
            const selectedOption = document.querySelector('input[name="question' + currentQuestionIndex + '"]:checked');
            if (selectedOption) {
                const correctAnswer = questions[currentQuestionIndex].correctAnswer;
                if (selectedOption.value === correctAnswer) {
                    alert('Risposta corretta!');
                } else {
                    alert('Risposta sbagliata!');
                }
                document.getElementById('next-question').style.display = 'inline-block'; // Mostra il pulsante per passare alla domanda successiva
            }
        });

        // Carica la prima domanda
        loadQuestion(currentQuestionIndex);
    }
});
