<?php

namespace ThemeRex\OpenAi;

class OpenAiAssistants extends OpenAi {

	public function __construct( $api_key )	{
		parent::__construct( $api_key );

		$this->setHeaders( array(
			'OpenAI-Beta: assistants=' . trx_addons_get_option( 'ai_helper_default_api_openai_assistants', 'v2' )
		) );
	}

	// ------------------- ASSISTANTS -------------------

	// List of assistants
	public function listAssistants( $args = array() ) {
		$url = trx_addons_add_to_url( Url::baseURL( "assistants" ), $args );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Create a new assistant
	public function createAssistant( $assistantData ) {
		$url = Url::baseURL( "assistants" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $assistantData );
	}

	// Retrieve an assistant
	public function retrieveAssistant( $assistantId ) {
		$url = Url::baseURL( "assistants/{$assistantId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Update an assistant
	public function updateAssistant( $assistantId, $assistantData ) {
		$url = Url::baseURL( "assistants/{$assistantId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $assistantData );
	}

	// Delete an assistant
	public function deleteAssistant( $assistantId ) {
		$url = Url::baseURL( "assistants/{$assistantId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'DELETE' );
	}

	// List assistant files
	public function listAssistantFiles( $assistantId, $args = array() ) {
		$url = trx_addons_add_to_url( Url::baseURL( "assistants/{$assistantId}/files" ), $args );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Create (assign) an assistant file
	public function createAssistantFile( $assistantId, $fileId ) {
		$url = Url::baseURL( "assistants/{$assistantId}/files" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', array( 'file_id' => $fileId ) );
	}

	// Retrieve an assistant file
	public function retrieveAssistantFile( $assistantId, $fileId ) {
		$url = Url::baseURL( "assistants/{$assistantId}/files/{$fileId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET');
	}

	// Delete (unassign) an assistant file
	public function deleteAssistantFile( $assistantId, $fileId ) {
		$url = Url::baseURL( "assistants/{$assistantId}/files/{$fileId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'DELETE' );
	}


	// ------------------- THREADS -------------------

	// Create a new thread associated with an assistant
	public function createThread( $threadData = array() ) {
		$url = Url::baseURL( "threads" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $threadData );
	}

	// Retrieve a thread data
	public function retrieveThread( $threadId ) {
		$url = Url::baseURL( "threads/{$threadId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Update a thread
	public function updateThread( $threadId, $threadData ) {
		$url = Url::baseURL( "threads/{$threadId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $threadData );
	}

	// Delete a thread
	public function deleteThread( $threadId ) {
		$url = Url::baseURL( "threads/{$threadId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'DELETE' );
	}


	// ------------------- MESSAGES -------------------

	// List messages from a thread
	public function listMessages( $threadId, $args = array() ) {
		$url = trx_addons_add_to_url( Url::baseURL( "threads/{$threadId}/messages" ), $args );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Create a new message within a thread
	public function createMessage( $threadId, $messageData ) {
		$url = Url::baseURL( "threads/{$threadId}/messages" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $messageData );
	}

	// Retrieve a message from a thread
	public function retrieveMessage( $threadId, $messageId ) {
		$url = Url::baseURL( "threads/{$threadId}/messages/{$messageId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET');
	}

	// Update a thread
	public function updateMessage( $threadId, $messageId, $messageData ) {
		$url = Url::baseURL( "threads/{$threadId}/messages/{$messageId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $messageData );
	}

	// List message files
	public function listMessageFiles( $threadId, $messageId, $args = array() ) {
		$url = trx_addons_add_to_url( Url::baseURL( "threads/{$threadId}/messages/{$messageId}/files" ), $args );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Retrieve a message file
	public function retrieveMessageFile( $threadId, $messageId, $fileId ) {
		$url = Url::baseURL( "threads/{$threadId}/messages/{$messageId}/files/{$fileId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET');
	}


	// ------------------- RUNS -------------------

	// List runs from a thread
	public function listRuns( $threadId, $args = array() ) {
		$url = trx_addons_add_to_url( Url::baseURL( "threads/{$threadId}/runs" ), $args );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Create a new run associated with a thread
	public function createRun( $threadId, $runData ) {
		$url = Url::baseURL( "threads/{$threadId}/runs" );
		$this->baseUrl($url);
		return $this->sendRequest( $url, 'POST', $runData );
	}

	// Create a new thread and a run associated with this thread
	public function createThreadAndRun( $runData ) {
		$url = Url::baseURL( "threads/runs" );
		$this->baseUrl($url);
		return $this->sendRequest( $url, 'POST', $runData );
	}

	// Retrieve a run from a thread
	public function retrieveRun( $threadId, $runId ) {
		$url = Url::baseURL( "threads/{$threadId}/runs/{$runId}" );
		$this->baseUrl($url);
		return $this->sendRequest( $url, 'GET' );
	}

	// Update a run in the thread
	public function updateRun( $threadId, $runId, $runData ) {
		$url = Url::baseURL( "threads/{$threadId}/runs/{$runId}" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $runData );
	}

	// Submit tool outputs to the run
	public function submitToolOutputsToRun( $threadId, $runId, $toolData ) {
		$url = Url::baseURL( "threads/{$threadId}/runs/{$runId}/submit_tool_outputs" );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'POST', $toolData );
	}

	// Cancel a run from a thread
	public function cancelRun( $threadId, $runId ) {
		$url = Url::baseURL( "threads/{$threadId}/runs/{$runId}/cancel" );
		$this->baseUrl($url);
		return $this->sendRequest( $url, 'POST' );
	}


	// ------------------- RUN STEPS -------------------

	// List run steps from a thread
	public function listRunSteps( $threadId, $runId, $args = array() ) {
		$url = trx_addons_add_to_url( Url::baseURL( "threads/{$threadId}/runs/{$runId}/steps" ), $args );
		$this->baseUrl( $url );
		return $this->sendRequest( $url, 'GET' );
	}

	// Retrieve a run from a thread
	public function retrieveRunStep( $threadId, $runId, $stepId ) {
		$url = Url::baseURL( "threads/{$threadId}/runs/{$runId}/steps/{$stepId}" );
		$this->baseUrl($url);
		return $this->sendRequest( $url, 'GET' );
	}
}
