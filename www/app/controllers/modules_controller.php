<?php
class ModulesController extends AppController {

	var $name = 'Modules';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Module->recursive = 0;
		$this->set('modules', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Module', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('module', $this->Module->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Module->create();
			if ($this->Module->save($this->data)) {
				$this->Session->setFlash(__('The Module has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Module could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Module', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Module->save($this->data)) {
				$this->Session->setFlash(__('The Module has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Module could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Module->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Module', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Module->del($id)) {
			$this->Session->setFlash(__('Module deleted', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('The Module could not be deleted. Please, try again.', true));
		$this->redirect(array('action' => 'index'));
	}

}
?>