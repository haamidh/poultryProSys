<?php
interface crud
{
    public function create($user_id);
    public function read($user_id);
    public function update($user_id, $id);
    public function delete($user_id, $id);
    public function readOne();
}
