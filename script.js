document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('expense-form');
    const expenseList = document.getElementById('expense-list');
    const totalPriceElement = document.getElementById('total_price');
    const modal = document.getElementById('expense-modal');
    const openModalBtn = document.getElementById('open-modal');
    const closeModal = document.querySelector('.close');
    const cancelModal = document.querySelector('.cancel-btn');
    const formMessage = document.getElementById('form-message');

    // Pop-up message area
    const successModal = document.getElementById('success-modal');
    const successMessage = document.getElementById('success-message');
    const successCloseBtn = document.getElementById('success-close');

    function showModalMessage(message, isSuccess) {
        formMessage.textContent = message;
        formMessage.style.color = isSuccess ? 'green' : 'red';
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            things: document.getElementById('things').value,
            price: document.getElementById('price').value
        };
        
        try {
            const response = await fetch('http://localhost/expenses/api/v1/data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                showModalMessage('Successfully added item!', true);
                fetchExpenses(); 
                form.reset();  

                closeModal.click();

                successMessage.textContent = 'Item inserted successfully!';
                successModal.style.display = 'block';
            } else {
                showModalMessage('Failed to add expense.', false);
            }
        } catch (error) {
            showModalMessage('Failed to add expense.', false);
        }
    });

    openModalBtn.addEventListener('click', () => {
        formMessage.textContent = '';
        formMessage.style.color = '';
        modal.style.display = 'block';
    });

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });
    
    cancelModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    successCloseBtn.addEventListener('click', () => {
        successModal.style.display = 'none';
    });

    async function fetchExpenses() {
        try {
            const response = await fetch('http://localhost/expenses/api/v1/data'); 
            const data = await response.json();
            
            if (data.status === 'success') {
                const { total_price, data: expenses } = data;
    
                totalPriceElement.textContent = `$${parseFloat(total_price).toFixed(2)}`;
    
                expenseList.innerHTML = expenses.map(exp => `
                    <li class="expense-item">
                        <span class="item-name">${exp.things}</span>
                        <span class="item-price">$${parseFloat(exp.price).toFixed(2)}</span>
                        <button id="delete-item" class="delete-item" data-expense-id="${exp.id}"><i class="fa-solid fa-xmark"></i></button>
                    </li>
                `).join('');
                
                attachDeleteListeners();
            } else {
                console.error('Error:', data.message);
            }
        } catch (error) {
            console.error('Error fetching expenses:', error);
        }
    }

    function attachDeleteListeners() {
        const deleteButtons = document.querySelectorAll('.delete-item');
        deleteButtons.forEach(button => {
            button.addEventListener('click', async () => {
                const expenseId = button.getAttribute('data-expense-id');
                try {
                    const deleteResponse = await fetch(`http://localhost/expenses/api/v1/data?id=${expenseId}`, {
                        method: 'DELETE'
                    });

                    if (deleteResponse.ok) {
                        showModalMessage('Successfully deleted item!', true);
                        fetchExpenses(); // Refresh expenses after deletion
                    } else {
                        showModalMessage('Failed to delete item.', false);
                    }
                } catch (error) {
                    showModalMessage('Failed to delete item.', false);
                }
            });
        });
    }

    fetchExpenses();
});
