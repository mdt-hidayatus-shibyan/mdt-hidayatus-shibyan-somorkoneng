import 'package:flutter_test/flutter_test.dart';
import 'package:mdt_hidayatus_shibyan_mobile/main.dart';

void main() {
  testWidgets('App smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const MDTHidayatusShibyanApp());
    await tester.pump(const Duration(seconds: 3));
    expect(find.byType(MDTHidayatusShibyanApp), findsOneWidget);
  });
}




