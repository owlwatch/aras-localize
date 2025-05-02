import { defineStore } from "pinia";

// extend window typescript with "wpApiSettings" object
declare global {
	interface Window {
		wpApiSettings: {
			root: string;
			nonce: string;
			version: string;
			siteUrl: string;
			apiUrl: string;
		};
	}
}

const useForumsStore = defineStore("forums", () => {

	const {root, nonce} = window.wpApiSettings;

	
	const api = async ( path: string, body: {} = {}, method: string = 'GET') => {
		return rest('api/'+path, body, method);
	};

	const rest = async ( path: string, body: {} = {}, method: string = 'GET') => {
		let url = `${root}aras-verint/v1/${path}`;
		
		if( method == 'GET') {
			// if method is GET, append the body as query parameters
			const queryParams = new URLSearchParams(body as any).toString();
			if (queryParams) {
				url += `?${queryParams}`;
			}
		}

		return fetch(url, {
			method,
			headers: {
				'Content-Type': 'application/json',
				'X-WP-Nonce': nonce
			},
			body: method === 'POST' ? JSON.stringify(body) : null
		})
			.then((response) => {
				if (!response.ok) {
					throw new Error(`HTTP error! status: ${response.status}`);
				}
				return response.json();
			})
			.catch((error) => {
				console.error('Error:', error);
			});
	};

	return {
		api,
		rest
	};

});

export {useForumsStore};