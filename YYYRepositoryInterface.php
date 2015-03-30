<?php
namespace YYY\Model;

// This interface specifies a repository to handle yyy.
interface YYYRepositoryInterface
{
	public function get($attributY1);
        public function check(YYY $yyy);
	public function getAll();
	public function save(YYY $yyy);
	public function remove(YYY $yyy);
}