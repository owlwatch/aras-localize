import { defineStore } from 'pinia'

export const useGlossaryStore = defineStore({
  id: 'glossary',
  state: () => {

    const letters = ['All'];

    for (var i = 65; i <= 90; i++) {
      letters.push(String.fromCharCode(i));
    }
    return {
      letters,
      activeLetter: 'All',
      searchTerm: ''
    }
  },

  getters: {
    rows: () => {
      const elements = document.querySelectorAll('[data-glossary-terms] .glossary-row');
      // lets get all the terms
      const rows: Array<{letter: string, term: string, excerpt: string, element: Element}> = [];
      elements.forEach( (element: Element) => {
        const term = String(element.querySelector('dt')?.textContent).trim();
        const excerpt = String(element.querySelector('dd p')?.textContent).trim();
        const letter = term.substring(0,1).toUpperCase();
        rows.push({
          letter,
          term,
          element,
          excerpt
        });
      })
      return rows;
    },

    availableLetters(){
      const all: Array<string> = [];
      this.rows.forEach( (row) => {
        if( !all.includes(row.letter) ){
          all.push(row.letter)
        }
      });
      return all;
    }
  },

  actions: {
    setActiveLetter(letter: string){
      this.activeLetter = letter;
    }
  }
})
