<?php
interface crud
{
    public function create($user_id);
    public function read($user_id);
    public function update($id,$user_id);
    public function delete($id, $user_id);
    public function readOne();
}
