/**
 * Gestionnaire de panier pour l'application PlantShop.
 *
 * Ce fichier contient la classe `Cart`, qui gère toutes les interactions liées au panier :
 * - Ajout, suppression, et mise à jour des produits.
 * - Sauvegarde des données dans `localStorage`.
 * - Affichage du panier dans la barre de navigation et dans les pages dédiées.
 *
 * Dépendances :
 * - Axios (pour les requêtes HTTP si nécessaire).
 * - DOM API pour manipuler les éléments HTML.
 *
 * @class Cart
 */
class Cart {
	/**
	 * Récupère le contenu du panier depuis `localStorage`.
	 *
	 * @returns {Object} Contenu du panier sous forme d'objet JSON.
	 * @example
	 * const cart = new Cart();
	 * const contenu = cart.get();
	 */
	get() {
		try {
			return JSON.parse(localStorage.getItem("cart") || "{}");
		} catch (e) {
			console.error("Erreur JSON", e);
			return {};
		}
	}

	/**
	 * Sauvegarde le panier dans `localStorage`.
	 *
	 * @param {Object} cart Contenu du panier à sauvegarder.
	 * @example
	 * const cart = new Cart();
	 * cart.save({ 1: { id: 1, name: "Plante", price: 10, quantity: 2 } });
	 */
	save(cart) {
		localStorage.setItem("cart", JSON.stringify(cart));
	}

	/**
	 * Enregistre l'état du panier et met à jour l'affichage du compteur dans la navbar.
	 *
	 * @param {Object} cart - Objet représentant le contenu actuel du panier.
	 */
  #commitCart(cart) {
		this.save(cart);
		this.updateNavbarCount();
	}

	/**
	 * Ajoute un produit au panier.
	 *
	 * @param {number} id Identifiant unique du produit.
	 * @param {string} name Nom du produit.
	 * @param {number} price Prix unitaire du produit.
	 * @param {number} stock Quantité disponible en stock.
	 * @example
	 * const cart = new Cart();
	 * cart.add(1, "Plante Verte", 12.99, 10);
	 */
	add(id, name, price, stock) {
		const cart = this.get();
		if (!cart[id]) {
			cart[id] = { id, name, price, quantity: 0, stock };
		}

		if (cart[id].quantity >= stock) {
			showStockAlert(name, stock);
			setTimeout(() => {
				cart[id].quantity = stock;
				this.#commitCart(cart);
			}, 300);
		} else {
			cart[id].quantity += 1;
			this.#commitCart(cart);
		}
	}

	/**
	 * Met à jour la quantité d'un produit dans le panier.
	 *
	 * @param {number} id Identifiant unique du produit.
	 * @param {number|string} newQty Nouvelle quantité cible.
	 * @example
	 * const cart = new Cart();
	 * cart.update(1, 5);
	 */
	update(id, newQty) {
		const qty = parseInt(newQty);
		if (isNaN(qty)) return;

		const cart = this.get();
		if (!cart[id]) return;

		const input = document.querySelector(`input[data-cart-id="${id}"]`);
		const stock = parseInt(input?.dataset?.stock) || 1;

		let correctedQty = Math.max(1, Math.min(qty, stock));
		cart[id].quantity = correctedQty;
		if (input) input.value = correctedQty;

		this.save(cart);
		this.render();
	}

	/**
	 * Met à jour la quantité avec un délai (utilisé pour les champs de saisie).
	 *
	 * @param {number} id Identifiant unique du produit.
	 * @param {HTMLInputElement} inputElem Champ de saisie correspondant au produit.
	 * @example
	 * const input = document.querySelector('input[data-cart-id="1"]');
	 * cart.delayedUpdate(1, input);
	 */
	delayedUpdate(id, inputElem) {
		clearTimeout(inputElem._cartTimer);
		inputElem._cartTimer = setTimeout(() => {
			this.update(id, inputElem.value);
		}, 300);
	}

	/**
	 * Supprime un produit du panier.
	 *
	 * @param {number} id Identifiant unique du produit.
	 * @example
	 * const cart = new Cart();
	 * cart.remove(1);
	 */
	remove(id) {
		const cart = this.get();
		delete cart[id];
		this.save(cart);
		this.render();
	}

	/**
	 * Vide complètement le panier.
	 *
	 * @example
	 * const cart = new Cart();
	 * cart.clear();
	 */
	clear() {
		localStorage.removeItem("cart");
		this.render();
	}

	/**
	 * Met à jour le compteur d'articles dans la barre de navigation.
	 *
	 * @example
	 * const cart = new Cart();
	 * cart.updateNavbarCount();
	 */
	updateNavbarCount() {
		const cart = this.get();
		const count = Object.values(cart).reduce(
			(sum, item) => sum + item.quantity,
			0
		);
		const link = document.getElementById("cart-link");
		if (link) {
			link.innerText = `Mon Panier${count > 0 ? ` (${count})` : ""}`;
		}
	}

	/**
	 * Affiche le contenu complet du panier dans le conteneur HTML.
	 *
	 * @example
	 * const cart = new Cart();
	 * cart.render();
	 */
	render() {
		const container = document.getElementById("cart-container");
		if (!container) return;

		const cart = this.get();
		container.innerHTML = "";

		this.updateNavbarCount();

		if (Object.keys(cart).length === 0) {
			const alert = document.createElement("p");
			alert.className = "alert alert-info";
			alert.textContent = "Votre panier est vide.";
			container.appendChild(alert);
			const actions = document.getElementById("cart-actions");
			if (actions) {
				actions.classList.add("d-none");
			}
			return;
		}

		const table = document.createElement("table");
		table.className = "table";

		const thead = document.createElement("thead");
		thead.className = "table-danger";
		const headerRow = document.createElement("tr");
		["Plante", "Quantité", "Action"].forEach((text) => {
			const th = document.createElement("th");
			th.textContent = text;
			headerRow.appendChild(th);
		});
		thead.appendChild(headerRow);
		table.appendChild(thead);

		const tbody = document.createElement("tbody");
		let total = 0;

		for (const id in cart) {
			const item = cart[id];
			total += item.price * item.quantity;

			const row = document.createElement("tr");

			const tdName = document.createElement("td");
			const link = document.createElement("a");
			link.href = `/plantes/${id}`;
			link.className = "text-decoration-none";
			link.textContent = item.name;
			tdName.appendChild(link);

			const tdQty = document.createElement("td");
			const input = document.createElement("input");
			input.type = "number";
			input.min = "1";
			input.className = "form-control form-control-sm";
			input.style.maxWidth = "70px";
			input.value = item.quantity;
			input.dataset.cartId = id;
			input.dataset.stock = item.stock;
			input.oninput = () => this.delayedUpdate(id, input);
			tdQty.appendChild(input);

			const tdAction = document.createElement("td");
			const btn = document.createElement("button");
			btn.className = "btn btn-danger btn-sm";
			btn.textContent = "Retirer";
			btn.onclick = () => this.remove(id);
			tdAction.appendChild(btn);

			row.appendChild(tdName);
			row.appendChild(tdQty);
			row.appendChild(tdAction);
			tbody.appendChild(row);
		}

		table.appendChild(tbody);
		container.appendChild(table);

		const totalEl = document.createElement("p");
		totalEl.className = "text-end fw-bold";
		totalEl.textContent = `Total : ${total} €`;
		container.appendChild(totalEl);

		const actions = document.getElementById("cart-actions");
		if (actions) {
			actions.classList.toggle("d-none", Object.keys(cart).length === 0);
		}
	}

	/**
	 * Affiche le récapitulatif de commande dans la page nouvelle commande.
	 * Remplit aussi le champ masqué pour l'envoi du panier.
	 *
	 * @container HTMLElement cible contenant l'affichage de la commande
	 * @input     Champ hidden HTML pour sérialiser le panier
	 */
	renderOrderReview(
		containerId = "order-review-container",
		inputId = "order-items-input"
	) {
		const container = document.getElementById(containerId);
		const input = document.getElementById(inputId);
		if (!container || !input) return;

		const cart = this.get();

		if (Object.keys(cart).length === 0) {
			const alert = document.createElement("p");
			alert.className = "alert alert-warning";
			alert.textContent = "Votre panier est vide.";
			container.innerHTML = "";
			container.appendChild(alert);
			input.value = "";
			return;
		}

		const table = document.createElement("table");
		table.className = "table shadow";

		const thead = document.createElement("thead");
		thead.className = "table-danger";
		const headerRow = document.createElement("tr");
		["Plante", "Quantité", "Total"].forEach((text) => {
			const th = document.createElement("th");
			th.textContent = text;
			headerRow.appendChild(th);
		});
		thead.appendChild(headerRow);
		table.appendChild(thead);

		const tbody = document.createElement("tbody");
		let total = 0;
		const items = [];

		for (const id in cart) {
			const item = cart[id];
			const subtotal = item.price * item.quantity;
			total += subtotal;

			const row = document.createElement("tr");

			const tdName = document.createElement("td");
			const link = document.createElement("a");
			link.href = `/plantes/${item.id}`;
			link.className = "cart-plant-link";
			link.textContent = item.name;
			tdName.appendChild(link);

			const tdQty = document.createElement("td");
			tdQty.textContent = item.quantity;

			const tdTotal = document.createElement("td");
			tdTotal.textContent = `${subtotal} €`;

			row.appendChild(tdName);
			row.appendChild(tdQty);
			row.appendChild(tdTotal);

			tbody.appendChild(row);

			items.push({ plant_id: parseInt(id), quantity: item.quantity });
		}

		table.appendChild(tbody);
		container.innerHTML = "";
		container.appendChild(table);

		const totalEl = document.createElement("p");
		totalEl.className = "text-end fw-bold";
		totalEl.textContent = `Total : ${total} €`;
		container.appendChild(totalEl);

		input.value = JSON.stringify(items);
	}
}

// Initialisation

document.addEventListener("DOMContentLoaded", () => {
	window.Cart = new Cart();
	window.Cart.updateNavbarCount();
	window.Cart.render();

	// Vide le panier si succès détecté via flash
	if (document.querySelector(".alert-success")) {
		window.Cart.clear();
		window.Cart.updateNavbarCount();
	}
});

function showStockAlert(plantName, stock) {
	const alert = document.createElement("div");
	alert.className =
		"alert alert-warning fade position-absolute top-0 start-50 translate-middle-x mt-3 shadow";
	alert.role = "alert";
	alert.style.zIndex = "1055";
	alert.style.maxWidth = "600px";
	alert.style.pointerEvents = "none";
	const message = document.createTextNode(
		"Stock insuffisant pour pour cette plante ("
	);
	const strong = document.createElement("strong");
	strong.textContent = plantName;
	const message2 = document.createTextNode(
		`), actuellement, il en reste ${stock}.`
	);
	alert.appendChild(message);
	alert.appendChild(strong);
	alert.appendChild(message2);
	document.body.appendChild(alert);
	setTimeout(() => alert.classList.add("show"), 10);
	setTimeout(() => {
		alert.classList.remove("show");
		alert.classList.add("fade");
		setTimeout(() => alert.remove(), 300);
	}, 3000);
}
