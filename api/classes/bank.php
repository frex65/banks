<?php
//This class handles a single memory bank
class Bank
{
//Number of blocks in this bank
private $blocks = 0;

function __construct($numBlocks=0)
{
$this->blocks = $numBlocks;
} //constructor

//Method to get the current block count
public function blocks()
{
return $this->blocks;
} //blocks

//Add a block dduring reallocation
public function addBlock()
{
$this->blocks++;
} //addBlock

//Remove a block during reallocation
public function removeBlock()
{
if ($this->blocks > 0)
$this->blocks--;
} //removeBlock

//Remove all the blocks from this bank
//The reallocation process empties all blocks from the fullest bank and redistributes them
public function emptyBank()
{
$blocksRemoved = $this->blocks;
$this->blocks = 0;

//Return number of blocks removed so reallocation knows how many to redistribute
return $blocksRemoved;
} //emptyBank
} //Bank

//This class handles a collection of instances of the bank class above
class Banks
{
private $banks = [], $bankCount = 0, $states = [], $cycles = 0, $totalBlocks = 0;

//This function receives an array containing the number of blocks in each bank
//It creates an array of banks and allocates the correct number of blocks to each
function __construct($temp)
{
foreach($temp as $val)
{
$this->banks[] = new Bank((int)$val);

//Keeps a count of the total number of blocks in all banks
$this->totalBlocks += $val;
} //foreach

//Counts how many banks have been created
$this->bankCount = count($this->banks);

//Keeps a running log of how many banks in each block after each reallocation cycle
//Useful for working out whether the banks get stuck in a repeat loop
$this->addState($this->getCurrentstate());
} //constructor

//Returns the number of reallocation cycles that have been run
//Useful for debugging at what cycle the bad repeat loop kicks in
protected function getCycles()
{
return $this->cycles;
} //getCycles

//Works out the bank with the most blocks for reallocation purposes
//If more than one bank has the max number of bloccks, the first one is used
protected function fullestbank()
{
$max = 0;
$fullestBank = -1;

//Loop through all banks
foreach($this->banks as $key => $b)
{
//How many blocks are in this bank?
$blocks = $b->blocks();

//If more than the maxx found so far, this bank has the most blocks
if ($blocks > $max)
{
$max = $blocks;
$fullestBank = $key;
} //if
} //foreach

//Return the first bank with the most blocks
return $fullestBank;
} //fullestBank

//Takes a snapshot of the current state of all banks
//I.e. how many blocks are in each bank
//Returns the state as an array
protected function getCurrentState()
{
$temp = [];

foreach($this->banks as $val)
{
array_push($temp, $val->blocks());
} //foreach

return $temp;
} //getCurrentState

//Adds the latest current state to the running log after a reallocation cycle
protected function addState($newState)
{
array_push($this->states, $newState);
} //addState

//Compares the running log of bank states to see if a state has been repeated
//If so then we're in a bad repeat loop
//Returns the number of cycles when the repeat loop kicks in, or 0 if no repeat yet
protected function repeatStateAtCycle()
{
$states = $this->states;
$lastIndex = count($states)-1;

if ($lastIndex < 1)
return 0;

//Compares the latest state with all previous states
//We only need to compare the latest as this is run after each reallocation cycle
for ($j=0; $j<$lastIndex; $j++)
{
if ($states[$lastIndex] == $states[$j])
{
return $this->cycles;
} //if
} //for loop - j

return 0;
} //repeatStateAtCycle

//Perform a single reallocation cycle
protected function cycle()
{
//Find the fullest bank so we can empty and redistribute
$i = $this->fullestBank();

//Empty the fullest bank
$blocks = $this->banks[$i]->emptyBank();

//Redistribute one to the next and all subsequent banks until the stack is gone
while ($blocks > 0)
{
$i++;

//If we go past the number of banks, go back to 0
if ($i == $this->bankCount)
$i = 0;

$this->banks[$i]->addBlock();
$blocks--;
} //while

//Add the current state of all banks to the running log so we can check for repeats
$this->addState($this->getCurrentstate());

//Log that we've done another reallocation cycle
$this->cycles++;

//Check whether we're in a repeat loop and return the answer
return $this->repeatStateAtCycle();
} //cycle

//The function called by the API that runs reallocation cycles until a repeat loop is detected
public function reallocate()
{
$repeat = 0;

//If there aren't any blocks in the banks, return 0
if ($this->totalBlocks == 0)
return 0;

//Run reallocation cycles until a repeat loop happens
while ($repeat == 0)
{

//Run a single cycle and check for repeat
$repeat = $this->cycle();
} //while

//Return the cycle at which a repeat loop is detected, or 0 if none
return $repeat;
} //reallocate
} //Banks
?>