<?php
class LanguagesController extends AppController {

	var $name = 'Languages';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Language->recursive = 0;
		$this->set('languages', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Language', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('language', $this->Language->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Language->create();
			if ($this->Language->save($this->data)) {
				$this->Session->setFlash(__('The Language has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Language could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Language', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Language->save($this->data)) {
				$this->Session->setFlash(__('The Language has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Language could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Language->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Language', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Language->del($id)) {
			$this->Session->setFlash(__('Language deleted', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('The Language could not be deleted. Please, try again.', true));
		$this->redirect(array('action' => 'index'));
	}

}
?>