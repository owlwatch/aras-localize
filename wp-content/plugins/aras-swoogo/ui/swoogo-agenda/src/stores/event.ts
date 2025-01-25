import { ref, computed, type ComputedRef, watch } from 'vue'
import { defineStore } from 'pinia'
import {useScrollLock} from '@vueuse/core';

interface SwoogoField {
	id: number
	name: string
}

interface EventData {
	details: EventDetails
	sessions: Session[]
	sponsors: Sponsor[]
	speakers: Speaker[]
	tracks: Track[]
	locations: Location[]
	sessionFields: SwoogoField[]
	contactFields: SwoogoField[]
	sponsorFields: SwoogoField[]
}

interface Event {
	id: number
	sessionFields: SwoogoField[]
	contactFields: SwoogoField[]
	sponsorFields: SwoogoField[]
}

interface EventDetails {
	id: number
	name: string
	description: string
	date: string
	time: string
}

interface Session {
	id: number
	name: string
	description: string
	date: string
	start_time: string
	end_time: string
	location_id: number
	track_id: number
	speaker_ids: number[]
	eventIds?: number[]
}

interface Speaker {
	id: number
	first_name: string
	last_name: string
	bio: string
	image: string
	eventIds?: number[]
}

interface Sponsor {
	id: number
	name: string
	logo_id: string
	level?: {
		id: number,
		value: string
	}
	eventIds?: number[]
}

interface Track {
	id: number
	name: string
	eventIds?: number[]
}

interface Location {
	id: number
	name: string
	eventIds?: number[]
}

export const useEventStore = defineStore('event', () => {
	
	const events = ref<Event[]>( [] );
	const details = ref<EventDetails[]>( [] );
	const sessions = ref<Session[]>( [] );
	const sponsors = ref<Sponsor[]>( [] );
	const speakers = ref<Speaker[]>( [] );
	const tracks = ref<Track[]>( [] );
	const locations = ref<Location[]>( [] );
	const activeModalSession = ref<Session|null>( null );
	const activeModalSpeaker = ref<Speaker|null>( null );

	function addEvent( data: EventData ) {

		// lets add all of our data
		const eventId = data.details.id;

		// update the event details
		let found = details.value.find( d => d.id == eventId );
		if( found ){
			Object.assign( found, data.details );
		}
		else {
			details.value.push( data.details );
		}

		data.sponsors.forEach( sponsor => {
			let existing = sponsors.value.find( s => s.id == sponsor.id );
			if( existing ) {
				existing.eventIds?.push( eventId );
			}
			else {
				sponsor.eventIds = [eventId];
				sponsors.value.push( sponsor );
			}
		});

		data.speakers.forEach( speaker => {
			let existing = speakers.value.find( s => s.id == speaker.id );
			if( existing ) {
				existing.eventIds?.push( eventId );
			}
			else {
				speaker.eventIds = [eventId];
				speakers.value.push( speaker );
			}
		});

		data.tracks.forEach( track => {
			let existing = tracks.value.find( s => s.id == track.id );
			if( existing ) {
				existing.eventIds?.push( eventId );
			}
			else {
				track.eventIds = [eventId];
				tracks.value.push( track );
			}
		});

		data.locations.forEach( location => {
			let existing = locations.value.find( s => s.id == location.id );
			if( existing ) {
				existing.eventIds?.push( eventId );
			}
			else {
				location.eventIds = [eventId];
				locations.value.push( location );
			}
		});
		
		data.sessions.forEach( (session : Session) => {
			let existing = sessions.value.find( s => s.id == session.id );
			if( existing ) {
				existing.eventIds?.push( eventId );
			}
			else {
				session.eventIds = [eventId];
				sessions.value.push( session );
			}
		});

		sessions.value.sort( (a,b) => {
			if( a.date < b.date ) return -1;
			if( a.date > b.date ) return 1;
			if( a.start_time < b.start_time ) return -1;
			if( a.start_time > b.start_time ) return 1;
			// now sort by track name
			let aTrack = getSessionTrack(a);
			let bTrack = getSessionTrack(b);
			if( aTrack && bTrack ){
				if( aTrack.name < bTrack.name ) return -1;
				if( aTrack.name > bTrack.name ) return 1;
			}
			return 0;
		});

		events.value.push({
			id: eventId,
			sessionFields: data.sessionFields,
			contactFields: data.contactFields,
			sponsorFields: data.sponsorFields
		})
	}

	function setActiveModalSession( session: Session ) {
		activeModalSession.value = session;
	}

	function setActiveModalSpeaker( speaker: Speaker ) {
		activeModalSpeaker.value = speaker
	}

	function getNextSession( session: Session ) : Session | null{
		let event = events.value.find( e => session.eventIds?.includes(e.id));
		let sessions = getEventFilteredSessions( event as Event );
		if( !sessions ) return null;
		let index = sessions.findIndex( s => s.id == session.id );
		if( index == -1 ) return null;
		if( index == sessions.length - 1 ) return null;
		return sessions[index + 1];
	}

	function getPreviousSession( session: Session ) : Session | null {
		let event = events.value.find( e => session.eventIds?.includes(e.id));
		let sessions = getEventFilteredSessions( event as Event);
		if( !sessions ) return null;
		let index = sessions.findIndex( s => s.id == session.id );
		if( index == -1 ) return null;
		if( index == 0 ) return null;
		return sessions[index - 1];
	}

	function getSessionsBySpeaker( speaker: Speaker ) {
		return sessions.value.filter( s => s.speaker_ids?.includes( speaker.id ) );
	}

	function getSessionLocation( session: Session ){
		return locations.value.find( l => l.id == session.location_id );
	}

	function getSessionTrack( session: Session ){
		return tracks.value.find( t => t.id == session.track_id );
	}

	function getSessionSpeakers( session: Session ){
		return speakers.value.filter( s => session.speaker_ids?.includes( s.id ) );
	}

	function getEvent( eventId: number ) {
		return events.value.find( e => e.id == eventId );
	}

	function getEventSessions( event: Event | number ) {
		if( typeof event !== 'number' ) event = event.id;
		return sessions.value.filter( s => s.eventIds?.includes( event ) );
	}

	function getEventSponsors( event: Event | number ) {
		if( typeof event !== 'number' ) event = event.id;
		return sponsors.value.filter( s => s.eventIds?.includes( event ) );
	}

	function getEventSpeakers( event: Event | number ) {
		if( typeof event !== 'number' ) event = event.id;
		return speakers.value.filter( s => s.eventIds?.includes( event ) );
	}

	function getEventLocations( event: Event | number ) {
		if( typeof event !== 'number' ) event = event.id;
		return locations.value.filter( s => s.eventIds?.includes( event ) );
	}

	function getEventTracks( event: Event | number ) {
		if( typeof event !== 'number' ) event = event.id;
		return tracks.value.filter( s => s.eventIds?.includes( event ) );
	}

	function getEventFilteredSessions( event: Event | number ) {
		if( typeof event !== 'number' ) event = event.id;
		return sessions.value.filter( s => s.eventIds?.includes( event ) )
			.filter( s => !s.name.match(/speaker\sdinner/i) )
			.filter( s => !['Partner Session','Transition'].includes(getSessionTrack(s)?.name || '') );
	}

	function getEventDetails( eventId: number ) {
		return details.value.find( d => d.id == eventId );
	}

	return {
		events,
		details,
		sessions,
		activeModalSession,
		activeModalSpeaker,
		sponsors,
		speakers,
		tracks,
		locations,
		addEvent,
		getEvent,
		getNextSession,
		getPreviousSession,
		getSessionsBySpeaker,
		setActiveModalSession,
		setActiveModalSpeaker,
		getSessionLocation,
		getSessionTrack,
		getSessionSpeakers,
		getEventFilteredSessions,
		getEventDetails,
		getEventSessions,
		getEventSponsors,
		getEventTracks,
		getEventSpeakers,
		getEventLocations,
	}
})

export type { Event, EventDetails, Session, Speaker, Sponsor, Track, Location };