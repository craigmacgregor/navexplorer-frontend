<?php

namespace App\Navcoin\Block\Mapper;

use App\Navcoin\Block\Entity\Block;
use App\Navcoin\Block\Entity\BlockSignal;
use App\Navcoin\Block\Entity\BlockSignals;
use App\Navcoin\Common\Mapper\BaseMapper;

/**
 * Class BlockMapper
 *
 * @package App\Navcoin\Mapper
 */
class BlockMapper extends BaseMapper
{
    /**
     * Map block
     *
     * @param array $data
     *
     * @return Block
     */
    public function mapEntity(array $data): Block
    {
        return new Block(
            $data['id'],
            $data['hash'],
            $data['merkleRoot'],
            $data['bits'],
            $data['size'],
            $data['version'],
            $data['nonce'],
            $data['height'],
            $data['difficulty'],
            $data['confirmations'],
            (new \DateTime())->setTimestamp($data['created']/1000),
            $data['stake'] / 100000000,
            $data['fees'] / 100000000,
            $data['spend'] / 100000000,
            $data['stakedBy'] ?: '',
            $data['transactions'],
            $data['best'],
            $this->mapSignals(array_key_exists('signals', $data) ? $data['signals'] : []),
            $data['blockCycle']
        );
    }

    /**
     * Map Inputs
     *
     * @param array $data
     *
     * @return BlockSignals
     */
    private function mapSignals(array $data): BlockSignals
    {
        $signals = new BlockSignals();

        foreach ($data as $signalData) {
            $signals->add(
                new BlockSignal(
                    $signalData['name'],
                    $signalData['signalling']
                )
            );
        }

        return $signals;
    }
}
