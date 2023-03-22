<?php 

namespace Views;

class View {
    public function __construct(
        private string $fileName,
        string|array $params
    ) {
        if (is_array($params)) {
            foreach($params as $key=>$value) {
                ${$key} = $value;
            }
        }
        unset($params);
        $this->includeViewFile($this->getFileName());
    }

    private function getFileName() {
        return $this->fileName;
    }

    private function includeViewFile($fileName) {
        include_once $fileName;
    }
}
