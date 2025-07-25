let questionCount = 0;

function createQuestionBlock(number) {
  const div = document.createElement('div');
  div.classList.add('question-block');
  div.dataset.questionNumber = number;

  div.innerHTML = `
    <h3>Question <span class="question-number">${number + 1}</span></h3>
    <div class="form-group">
      <label for="question-text-${number}">Question</label>
      <input type="text" id="question-text-${number}" name="questions[]" placeholder="Enter your question" required>
    </div>
    <div class="answer-options">
      <h4>Answer Options</h4>
      ${[1, 2, 3, 4].map(i => `
        <div class="option-group">
          <input type="radio" id="option-${number}-${i}" name="correct_answer[${number}]" value="${i}" ${i === 1 ? 'checked' : ''}>
          <label for="option-${number}-${i}">Option ${i}</label>
          <input type="text" class="option-input" name="options[${number}][]" placeholder="Option ${i}" required>
        </div>
      `).join('')}
      <p class="hint">Select the radio button next to the correct answer</p>
    </div>
  `;
  return div;
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('questions-container');
  
  // Add the first question block only if the container is empty
  if (container.children.length === 0) {
    container.appendChild(createQuestionBlock(questionCount));
    questionCount++;
  }

  const addButton = document.querySelector('.add-question-btn');
  if (addButton) {
    addButton.addEventListener('click', function(e) {
      e.preventDefault();
      container.appendChild(createQuestionBlock(questionCount));
      questionCount++;
    });
  } else {
    console.error('Add Question button not found. Check the class name.');
  }
});



