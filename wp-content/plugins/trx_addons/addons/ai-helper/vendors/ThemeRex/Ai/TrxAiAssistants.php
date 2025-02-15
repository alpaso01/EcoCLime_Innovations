<?php

namespace ThemeRex\Ai;

class TrxAiAssistants extends Api {

	public function __construct( $api_key )	{
		parent::__construct( $api_key );
		$this->setAuthMethod( 'None' );
	}

	protected function request( $args = array() ) {
		$theme_info = trx_addons_get_theme_info( false );
		$params = apply_filters( 'trx_addons_filter_ai_helper_trx_ai_assistant_request_args', array(
						'action'     => 'ai-assistant',
						'key'        => get_option( sprintf( 'purchase_code_%s', get_template() ) ),
						'src'        => $theme_info['theme_pro_key'],
						'theme_slug' => $theme_info['theme_slug'],
						'theme_name' => $theme_info['theme_name'],
						'domain'     => trx_addons_remove_protocol( home_url(), true ),
						'rnd'		 => mt_rand(),
					) );
		$url = trx_addons_get_upgrade_url( $params );
		if ( substr( $url, 0, 4 ) != 'http' ) {
			$url = 'https:' . $url;
		}
		if ( is_array( $args['ai-data'] ) ) {
			$args['ai-data'] = base64_encode( serialize( $args['ai-data'] ) );
		}
		$response = $this->sendRequest(
						$url,
						'POST',
						array_merge(
							array(
								'content_type' => 'multipart/form-data',
							),
							$args
						),
						false
					);
		if ( is_serialized( $response ) ) {
			try {
				$response = trx_addons_unserialize( $response );
			} catch ( Exception $e ) {
			}
		}
		if ( ! empty( $response['error'] ) || empty( $response['data'] ) || ! is_array( $response['data'] ) ) {
			$response = array(
				'status' => 'error',
				'error' => array( 
					'message' => ! empty( $response['error'] ) ? $response['error'] : __( 'Unexpected response from API', 'trx_addons' )
				)
			);
		} else {
			$response = $response['data'];
		}
		return $response;
	}


	// ------------------- ADD SUPPORT -------------------

	// Extend a support period by the support key
	public function addSupportKey( $supportKey ) {
		return $this->request( array( 'ai-action' => 'addSupportKey', 'ai-data' => array( 'supportKey' => $supportKey ) ) );
	}


	// ------------------- ASSISTANTS -------------------

	// List of assistants
	public function listAssistants( $args = array() ) {
		return $this->request( array( 'ai-action' => 'listAssistants', 'ai-data' => array( 'args' => $args ) ) );
	}

	// Create a new assistant
	public function createAssistant( $assistantData ) {
		return $this->request( array( 'ai-action' => 'createAssistant', 'ai-data' => array( 'assistantData' => $assistantData ) ) );
	}

	// Retrieve an assistant
	public function retrieveAssistant( $assistantId ) {
		return $this->request( array( 'ai-action' => 'retrieveAssistant', 'ai-data' => array( 'assistantId' => $assistantId ) ) );
	}

	// Update an assistant
	public function updateAssistant( $assistantId, $assistantData ) {
		return $this->request( array( 'ai-action' => 'retrieveAssistant', 'ai-data' => array( 'assistantId' => $assistantId, 'assistantData' => $assistantData ) ) );
	}

	// Delete an assistant
	public function deleteAssistant( $assistantId ) {
		return $this->request( array( 'ai-action' => 'deleteAssistant', 'ai-data' => array( 'assistantId' => $assistantId ) ) );
	}

	// List assistant files
	public function listAssistantFiles( $assistantId, $args = array() ) {
		return $this->request( array( 'ai-action' => 'listAssistantFiles', 'ai-data' => array( 'assistantId' => $assistantId, 'args' => $args ) ) );
	}

	// Create (assign) an assistant file
	public function createAssistantFile( $assistantId, $fileId ) {
		return $this->request( array( 'ai-action' => 'createAssistantFile', 'ai-data' => array( 'assistantId' => $assistantId, 'fileId' => $fileId ) ) );
	}

	// Retrieve an assistant file
	public function retrieveAssistantFile( $assistantId, $fileId ) {
		return $this->request( array( 'ai-action' => 'retrieveAssistantFile', 'ai-data' => array( 'assistantId' => $assistantId, 'fileId' => $fileId ) ) );
	}

	// Delete (unassign) an assistant file
	public function deleteAssistantFile( $assistantId, $fileId ) {
		return $this->request( array( 'ai-action' => 'deleteAssistantFile', 'ai-data' => array( 'assistantId' => $assistantId, 'fileId' => $fileId ) ) );
	}


	// ------------------- THREADS -------------------

	// Create a new thread associated with an assistant
	public function createThread( $threadData = array() ) {
		return $this->request( array( 'ai-action' => 'createThread', 'ai-data' => array( 'threadData' => $threadData ) ) );
	}

	// Retrieve a thread data
	public function retrieveThread( $threadId ) {
		return $this->request( array( 'ai-action' => 'retrieveThread', 'ai-data' => array( 'threadId' => $threadId ) ) );
	}

	// Update a thread
	public function updateThread( $threadId, $threadData ) {
		return $this->request( array( 'ai-action' => 'updateThread', 'ai-data' => array( 'threadId' => $threadId, 'threadData' => $threadData ) ) );
	}

	// Delete a thread
	public function deleteThread( $threadId ) {
		return $this->request( array( 'ai-action' => 'deleteThread', 'ai-data' => array( 'threadId' => $threadId ) ) );
	}


	// ------------------- MESSAGES -------------------

	// List messages from a thread
	public function listMessages( $threadId, $args = array() ) {
		return $this->request( array( 'ai-action' => 'listMessages', 'ai-data' => array( 'threadId' => $threadId, 'args' => $args ) ) );
	}

	// Create a new message within a thread
	public function createMessage( $threadId, $messageData ) {
		return $this->request( array( 'ai-action' => 'createMessage', 'ai-data' => array( 'threadId' => $threadId, 'messageData' => $messageData ) ) );
	}

	// Retrieve a message from a thread
	public function retrieveMessage( $threadId, $messageId ) {
		return $this->request( array( 'ai-action' => 'retrieveMessage', 'ai-data' => array( 'threadId' => $threadId, 'messageId' => $messageId ) ) );
	}

	// Update a thread
	public function updateMessage( $threadId, $messageId, $messageData ) {
		return $this->request( array( 'ai-action' => 'updateMessage', 'ai-data' => array( 'threadId' => $threadId, 'messageId' => $messageId, 'messageData' => $messageData ) ) );
	}

	// List message files
	public function listMessageFiles( $threadId, $messageId, $args = array() ) {
		return $this->request( array( 'ai-action' => 'listMessageFiles', 'ai-data' => array( 'threadId' => $threadId, 'messageId' => $messageId, 'args' => $args ) ) );
	}

	// Retrieve a message file
	public function retrieveMessageFile( $threadId, $messageId, $fileId ) {
		return $this->request( array( 'ai-action' => 'retrieveMessageFile', 'ai-data' => array( 'threadId' => $threadId, 'messageId' => $messageId, 'fileId' => $fileId ) ) );
	}


	// ------------------- RUNS -------------------

	// List runs from a thread
	public function listRuns( $threadId, $args = array() ) {
		return $this->request( array( 'ai-action' => 'listRuns', 'ai-data' => array( 'threadId' => $threadId, 'args' => $args ) ) );
	}

	// Create a new run associated with a thread
	public function createRun( $threadId, $runData ) {
		return $this->request( array( 'ai-action' => 'createRun', 'ai-data' => array( 'threadId' => $threadId, 'runData' => $runData ) ) );
	}

	// Create a new thread and a run associated with this thread
	public function createThreadAndRun( $runData ) {
		return $this->request( array( 'ai-action' => 'createThreadAndRun', 'ai-data' => array( 'runData' => $runData ) ) );
	}

	// Retrieve a run from a thread
	public function retrieveRun( $threadId, $runId ) {
		return $this->request( array( 'ai-action' => 'retrieveRun', 'ai-data' => array( 'threadId' => $threadId, 'runId' => $runId ) ) );
	}

	// Update a run in the thread
	public function updateRun( $threadId, $runId, $runData ) {
		return $this->request( array( 'ai-action' => 'updateRun', 'ai-data' => array( 'threadId' => $threadId, 'runId' => $runId, 'runData' => $runData ) ) );
	}

	// Submit tool outputs to the run
	public function submitToolOutputsToRun( $threadId, $runId, $toolData ) {
		return $this->request( array( 'ai-action' => 'submitToolOutputsToRun', 'ai-data' => array( 'threadId' => $threadId, 'runId' => $runId, 'toolData' => $toolData ) ) );
	}

	// Cancel a run from a thread
	public function cancelRun( $threadId, $runId ) {
		return $this->request( array( 'ai-action' => 'cancelRun', 'ai-data' => array( 'threadId' => $threadId, 'runId' => $runId ) ) );
	}


	// ------------------- RUN STEPS -------------------

	// List run steps from a thread
	public function listRunSteps( $threadId, $runId, $args = array() ) {
		return $this->request( array( 'ai-action' => 'listRunSteps', 'ai-data' => array( 'threadId' => $threadId, 'runId' => $runId, 'args' => $args ) ) );
	}

	// Retrieve a run from a thread
	public function retrieveRunStep( $threadId, $runId, $stepId ) {
		return $this->request( array( 'ai-action' => 'retrieveRunStep', 'ai-data' => array( 'threadId' => $threadId, 'runId' => $runId, 'stepId' => $stepId ) ) );
	}
}
