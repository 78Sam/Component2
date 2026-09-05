from typing import Any


class Test:

    valid: bool

    result: dict[Any, Any]

    def __init__(
            self,
            provided: dict[Any, Any],
        ) -> None:
        if 'sam' not in provided:
            self.valid = False
        else:
            self.result = provided
            self.valid = True
        # self.provided = provided


def testme(expected: dict[str, str], provided: dict[Any, Any]) -> Test:
    ...

def getarray() -> dict[Any, Any]:
    ...

result = testme(
    {'sam': 'string'},
    getarray(),
)

if result.valid:
    raise Exception('bad')

print(result)